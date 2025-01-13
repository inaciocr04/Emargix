<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class ApiController extends Controller
{

    public $login, $mdp, $proxy_login, $proxy_mdp, $proxy_url, $server, $protocol, $function, $sessionid, $url;


    public function __construct()
    {
        $this->setLogin(config('ade.login'));
        $this->setMdp(config('ade.password'));
        $this->setServer(config('ade.server'));
        $this->setProtocol(config('ade.protocol'));

        if (config('ade.useProxy')) {
            $this->setProxyLogin(config('ade.proxy_login'));
            $this->setProxyMdp(config('ade.proxy_password'));
            $this->setProxyUrl(config('ade.proxy_url'));
        }
    }

    public function connect()
    {
        if (config('ade.useProxy')) {
            $this->getUrlProxyBase(false);
        } else {
            $this->getUrlBase(false);
        }
        $this->addFunction("connect", true);
        $this->addElementUrl("login", $this->getLogin());
        $this->addElementUrl("password", $this->getMdp());

        if (config('ade.useProxy')) {
            $retour = Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                $xmlData = $retour->body();
                $matches = [];
                if (preg_match('/<session id="([^"]+)"/', $xmlData, $matches)) {
                    $id = $matches[1];
                    $this->setSessionid($id);
                }
            }
        } else {
            $retour = simplexml_load_file($this->url);
            $id = (string) $retour->attributes()->id;
            $this->setSessionid($id);
        }
        return $this->getSessionid();
    }


    public function disconnect(){
        if (config('ade.useProxy')){
            $this->getUrlProxyBase();
            $this->addFunction('disconnect');
            $retour=Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                $xmlData = $retour->body();
                $matches = [];
                if (preg_match('/<disconnected sessionId="([^"]+)"/', $xmlData, $matches)) {
                    $id = $matches[1];
                    if ($id == $this->getSessionid()){
                        $this->setSessionid(null);
                        return "Vous êtes bien deconnecté du service";
                    } else {
                        return "La deconnection n'a pas eu lieu";
                    }
                }
            }
        } else {
            $this->getUrlBase();
            $this->addFunction('disconnect');
            $retour=simplexml_load_file($this->getUrl());
            if ($this->getSessionid()!=$retour->attributes()->sessionId){
                return "La deconnection n'a pas eu lieu";
            } else{
                return "Vous êtes bien deconnecté du service";
            }
        }
    }

    /** Renvoi des information sur les différents projets
     * Le paramètre détail permet de spécifier le niveau de détail de la trame xml retournée :
    *- 1: id
    *- 2: & name
    *- 3: & version
    *- 4: & loaded
    *- 5: & nb_connected
    *- 6: & rights
    *- 7 or 0: & permissions de chaque utilisateurs (peut être énorme s’il y a de nombreux utilisateurs)

     * @param $url
     * @param null $idProject
     * @param null $detail
     * @param null $sessionId
     * @return string
     */
    public function getProject($sessionId=null, $idProject=null, $detail=null){
        if (config('ade.useProxy')){
            $this->getUrlProxyBase();
            $this->addFunction("getProjects");
            if (!is_null($idProject)) $this->addElementUrl( "id", $idProject);
            if (!is_null($detail)) $this->addElementUrl( "detail", $detail);
            if (!is_null($sessionId)) $this->addElementUrl( "sessionId", $sessionId);
            $retour=Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                return $retour;
            }
        } else {
            $this->getUrlBase();
            $this->addFunction("getProjects");
            if (!is_null($idProject)) $this->addElementUrl( "id", $idProject);
            if (!is_null($detail)) $this->addElementUrl( "detail", $detail);
            if (!is_null($sessionId)) $this->addElementUrl( "sessionId", $sessionId);
            $retour=simplexml_load_file($this->getUrl());
            return $retour;
        }
    }

    /**
     * Retrouve parmis la liste des projets celui de l'unistra
     * Renvoi le projet
     */
    static public function retrieveProjectUnistra($projects, $pattern){
        $k=[];
        $i=0;
        foreach ($projects as $key=>$project){
//            $name = (string) $project->attributes()->name;
            if(stripos((string) $project->attributes()->name[0], $pattern)!==false) $k[]=$i;
            $i++;
        }
        if (count($k)==0){
            throw new NotGoodValueException("Impossible de retrouver le projet");
        } elseif (count($k)>1){
            throw new NotGoodValueException("Plusieurs projets correspondent au pattern. Il faut être plus précis");
        }else{
            return $projects->project[$k[0]];
        }
    }

    public function setProject($idProject){
        if (config('ade.useProxy')){
            $this->getUrlProxyBase();
            $this->addFunction("setProject");
            $this->addElementUrl( "projectId", $idProject);
            $retour=Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                return $retour;
            }
        } else {
            $this->getUrlBase();
            $this->addFunction("setProject");
            $this->addElementUrl( "projectId", $idProject);
            $retour=simplexml_load_file($this->getUrl());
            if ($retour->attributes()->sessionId!=$this->getSessionid() && $retour->attributes()->projectId!=$idProject) throw new NotGoodValueException("impossible de définir le projet");
            return $retour;
        }

    }

    /**
     *Fonction : getResources Renvoie des ressources
    *Paramètres obligatoires : sessionId
    *Paramètres optionnels : tree, folders, leaves, id, name, category, type, email, url, size, quantity, code, address1,
    *address2, zipCode, state, city, country, telephone, fax, timezone, jobCategory, manager, codeX, codeY, codeZ, info,
    *detail
     *
     * tree Booleén Récupération des ressources sous forme de liste ou d’arbre, par défaut false (liste)
    *folders Booléen Récupération des dossiers, par défaut true
    *leaves Booléen Récupération des ressources feuilles, par défaut true
    *catégory Chaîne de caractères Filtrer sur la catégorie, les valeurs possible sont : all (par défaut), trainee,
    *instructor, classroom, equipment, category5, category6, category7, category8
    *fatherIds Chaine de caractères
    *(des entiers)
    *Filtrer sur des dossiers de ressources, la valeur peut être composé de plusieurs id
    *séparés par de |, par exemple : 12|15|123
    *Les autres Chaîne de caractères Filtre sur le champ correspondant, on peut écrire plusieurs valeurs séparés par des
    *|, dans ce cas, toutes les ressources correspondant à une de ces valeurs seront
    *retournées.
     * Le paramètre détail permet de spécifier le niveau de détail de la trame xml retournée :
    *- 1: id
    *- 2: & name
    *- 3: & category & path
    *- 4: & isGroup
    *- 5: & type
    *- 6: & email
    *- 7: & url
    *- 8: & size
    *- 9: & nb & nbEventsPlaced & durationInMinutes & firstWeek & firstDay & firstSlot & lastWeek & lastDay & lastSlot &
    *creation & lastUpdate
    *- 10: & fatherId & fatherName
    *- 11: & color & code & address1 & address2 & zipCode & state & city & country & telephone & fax & timezone &
    *jobCategory & manager & codeX & codeY & codeZ & info
    *- 12: & rights & owner & levelAccess
    *- 13 or 0: & contraints
     */
    public function getRessources(...$wheres){
        if (config('ade.useProxy')){
            $this->getUrlProxyBase();
            $this->addFunction('getResources');
            foreach ($wheres as $w){
                $this->addElementUrl($w[0], $w[1]);
            }
            $retour=Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                return $retour;
            }
        } else {
            $this->getUrlBase();
            $this->addFunction('getResources');
            foreach ($wheres as $w){
                $this->addElementUrl($w[0], $w[1]);
            }
            $retour=simplexml_load_file($this->getUrl());
            return $retour;
        }
    }

    /** Renvoi tous les enseignants
     * @param mixed ...$wheres Voir getRessources pour l'argument where
     * @return SimpleXMLElement
     *
     * @throws ConnectionException
     */
    public function getAllProf($sessionId)
    {
        if (config('ade.useProxy')) {
            $this->getUrlProxyBase();
            $this->addFunction("getResources");
            $this->addElementUrl("category", "instructor");
            $this->addElementUrl('sessionId', $sessionId);
            $this->addElementUrl('jobCategory', 'IHA');
            $this->addElementUrl('detail', '6');

            $retour = Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);

            if ($retour->successful()) {
                // Conversion de la réponse JSON en tableau associatif
                $data = $retour->json();

                // S'assurer que la clé 'instructor' existe et qu'il contient un tableau
                if (isset($data['instructor']) && is_array($data['instructor'])) {
                    return $data['instructor']; // On retourne les instructeurs directement
                }

                // Si pas de données 'instructor', retourner un tableau vide
                return [];
            }
        } else {
            $this->getUrlBase();
            $this->addFunction("getResources");
            $this->addElementUrl("category", "instructor");
            $this->addElementUrl('sessionId', $sessionId);
            $this->addElementUrl('jobCategory', 'IHA');
            $this->addElementUrl('detail', '6');

            $retour = simplexml_load_file($this->getUrl());
            if ($retour === false || !isset($retour->instructor)) {
                return [];
            }

            $instructors = [];
            foreach ($retour->instructor as $instructor) {
                // Récupère les attributs id et name
                $instructors[] = [
                    'id' => (string) $instructor['id'],  // Accès à l'attribut 'id'
                    'name' => (string) $instructor['name'],  // Accès à l'attribut 'name'
                    'email' => (string) $instructor['email']  // Accès à l'attribut 'email'
                ];
            }

            return $instructors;
        }
    }

    public function getPlanningProf($sessionId)
    {
        // Vérifier si l'utilisateur est connecté
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Utilisateur non connecté.');
        }

        // Récupérer le professeur associé à l'utilisateur
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher || !$teacher->professor_id) {
            abort(404, 'Aucun identifiant de professeur trouvé pour cet utilisateur.');
        }

        $professorId = $teacher->professor_id;

        // Définir le début de l'année scolaire au 19 août
        $currentYear = now()->year;

        // Définir la date de début de l'année scolaire comme étant le 19 août de l'année en cours
        $startSchoolYear = Carbon::create($currentYear, 8, 19);

        // Si la date actuelle est avant le 19 août de l'année en cours, on prend le 19 août de l'année précédente
        if (now()->lessThan($startSchoolYear)) {
            $startSchoolYear = Carbon::create($currentYear - 1, 8, 19);
        }
        // Calculer la semaine de cours relative à la date de début de l'année scolaire
        $currentWeek = $startSchoolYear->diffInWeeks(now(), false) + 1;

        // Assurer que la semaine courante commence à 1, même avant le 19 août
        $currentWeek = max(1, intval($currentWeek));

        // Construire l'URL pour récupérer les événements
        $this->getUrlBase(); // Appel de la fonction de base pour obtenir l'URL
        $this->addFunction('getEvents');
        $this->addElementUrl('sessionId', $sessionId); // Ajouter l'ID de la session
        $this->addElementUrl('resources', $professorId); // Ajouter l'ID du professeur
        $this->addElementUrl('weeks', $currentWeek); // Ajouter la semaine actuelle
        $this->addElementUrl('detail', '8'); // Ajouter le détail des événements

        // Récupérer la réponse de l'API
        $url = $this->getUrl();
        $response = file_get_contents($url);
        $retour = simplexml_load_string($response);

        // Vérifier si l'API a renvoyé une erreur
        if (isset($retour->error)) {
            dd('Erreur de l\'API : ' . (string)$retour->error['name']);
        }

        // Initialiser le tableau des événements
        $events = [];

        // Extraire les événements à partir de la réponse
        foreach ($retour->event as $event) {
            $trainees = [];
            $classrooms = [];

            foreach ($event->resources->resource as $resource) {
                $category = (string) $resource['category'];
                $name = (string) $resource['name'];

                if ($category === 'trainee') {
                    $trainees[] = $name;
                } elseif ($category === 'classroom') {
                    $classrooms[] = $name;
                }
            }

            $events[] = [
                'id' => (string) $event['id'],  // Accéder à l'attribut 'id'
                'name' => (string) $event['name'],  // Accéder à l'attribut 'name'
                'date' => (string) $event['date'],  // Accéder à l'attribut 'date'
                'startHour' => (string) $event['startHour'],  // Accéder à l'attribut 'startHour'
                'endHour' => (string) $event['endHour'],  // Accéder à l'attribut 'endHour'
                'day' => (string) $event['day'],  // Accéder à l'attribut 'endHour'
                'trainees' => $trainees, // Ajouter les trainees
                'classrooms' => $classrooms, // Ajouter les classrooms
            ];
        }

        // Si aucun événement n'a été trouvé, retourner un tableau vide
        if (empty($events)) {
            return [];
        }

        usort($events, function ($a, $b) {
            // Assurez-vous que les dates sont au format ISO (YYYY-MM-DD)
            $dateA = DateTime::createFromFormat('d/m/Y', $a['date']) ?: new DateTime($a['date']);
            $dateB = DateTime::createFromFormat('d/m/Y', $b['date']) ?: new DateTime($b['date']);

            $dateTimeA = $dateA->format('Y-m-d') . ' ' . $a['startHour'];
            $dateTimeB = $dateB->format('Y-m-d') . ' ' . $b['startHour'];

            return strtotime($dateTimeA) - strtotime($dateTimeB);
        });

        return $events;
    }

    public function getEventPlanningId($sessionId, $eventId)
    {

        // Définir le début de l'année scolaire au 19 août
        $startSchoolYear = now()->year . '-08-19';
        $startSchoolYear = Carbon::parse($startSchoolYear);

        // Calculer la semaine de cours relative à la date de début de l'année scolaire
        $currentWeek = $startSchoolYear->diffInWeeks(now(), false) + 1;

        // Assurer que la semaine courante commence à 1, même avant le 19 août
        $currentWeek = max(1, intval($currentWeek));


        // Construire l'URL pour récupérer les événements
        $this->getUrlBase(); // Appel de la fonction de base pour obtenir l'URL
        $this->addFunction('getEvents');
        $this->addElementUrl('sessionId', $sessionId); // Ajouter l'ID de la session
        $this->addElementUrl('eventId', $eventId); // Ajouter l'ID du professeur
        $this->addElementUrl('weeks', $currentWeek); // Ajouter la semaine actuelle
        $this->addElementUrl('detail', '8'); // Ajouter le détail des événements

    }

    public function getAllTraining($sessionId)
    {
        if (config('ade.useProxy')) {
            $this->getUrlProxyBase();
            $this->addFunction("getResources");
            $this->addElementUrl("category", "trainee");
            $this->addElementUrl('sessionId', $sessionId);
            $this->addElementUrl('jobCategory', 'IHA');
            $this->addElementUrl('detail', '9'); // Niveau de détail ajusté

            $retour = Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);

            if ($retour->successful()) {
                // Conversion de la réponse JSON en tableau associatif
                $data = $retour->json();

                // S'assurer que la clé 'trainee' existe et qu'il contient un tableau
                if (isset($data['trainee']) && is_array($data['trainee'])) {
                    // Filtrer les trainees avec lastSlot != -1
                    return array_map(function($trainee) {
                        return [
                            'id' => $trainee['id'],
                            'name' => $trainee['name'],
                        ];
                    }, array_filter($data['trainee'], function($trainee) {
                        return isset($trainee['lastSlot']) && $trainee['lastSlot'] != -1;
                    }));
                }

                // Si pas de données 'trainee', retourner un tableau vide
                return [];
            }
        } else {
            $this->getUrlBase();
            $this->addFunction("getResources");
            $this->addElementUrl("category", "trainee");
            $this->addElementUrl('sessionId', $sessionId);
            $this->addElementUrl('jobCategory', 'IHA');
            $this->addElementUrl('detail', '9'); // Niveau de détail ajusté

            $retour = simplexml_load_file($this->getUrl());
            if ($retour === false || !isset($retour->trainee)) {
                return [];
            }

            $trainees = [];
            foreach ($retour->trainee as $trainee) {
                // Ajouter les éléments si lastSlot != -1
                if ((int)$trainee['lastSlot'] != -1) {
                    $trainees[] = [
                        'id' => (string)$trainee['id'],  // Accès à l'attribut 'id'
                        'name' => (string)$trainee['name'],  // Accès à l'attribut 'name'
                    ];
                }
            }

            return $trainees;
        }
    }








    /** Renvoi tous les événements
     * @param mixed ...$wheres Voir getRessources pour l'argument where
     * @return SimpleXMLElement
     *eventId numérique Id de l’event
    *name Chaîne de caractère Nom de l’activité de l’event on peut écrire plusieurs valeurs séparés par des |
    *activities Numérique Id de l’activité, on peut écrire plusieurs valeurs séparés par des |
    *resources Numérique Id d’une ressource, revoie les events ou la ressource a été affecté. On peut
    *écrire plusieurs valeurs séparés par des |
    *weeks Numérique Numéro des semaines. On peut écrire plusieurs valeurs séparés par des |
    *days Numérique Numéro des jours. On peut écrire plusieurs valeurs séparés par des |
    *date Chaîne de caractère Date formatée comme ceci : MM/dd/yyyy. Retourne les events qui ont lieu à
    *cette date
    *startDate Chaîne de caractère Date formatée comme ceci : MM/dd/yyyy. Retourne les events qui ont lieu à
    *partir de cette date
    *endDate Chaîne de caractère Date formatée comme ceci : MM/dd/yyyy. Retourne les events qui ont lieu
    *jusqu'à cette date
     *
     * Le paramètre détail permet de spécifier le niveau de détail de la trame xml retournée :
    *- 1: id, activityId, externalId, session, repetition
    *- 2: & name
    *- 3: & week, day, slot, absoluteSlot, date, startHour, endHour
    *- 4: & duration
    *- 5: & color, note
    *- 6: & isLockPosition
    *- 7: & isLockResources & creation & lastUpdate
    *- 8: & resources
     */
    public function getEvent(...$wheres){
        if (config('ade.useProxy')){
            $this->getUrlProxyBase();
            $this->addFunction('getEvents');
            foreach ($wheres as $w){
                $this->addElementUrl($w[0], $w[1]);
            }
            $retour=Http::withBasicAuth($this->getProxyLogin(), $this->getProxyMdp())->get($this->url);
            if ($retour->successful()) {
                return $retour;
            }
        } else {
            $this->getUrlBase();
            $this->addFunction('getEvents');
            foreach ($wheres as $w){
                $this->addElementUrl($w[0], $w[1]);
            }
            $retour=simplexml_load_file($this->getUrl());
            return $retour;
        }
    }

    static public function retrieveEventPromotion($ressource, $datedebut, $datefin, $detail){
        $u=AuthService::getUser();
        if (!AuthService::canDo($u, "acceder_gestion_structure")) throw new NotAllowedException('Vous n\'avez pas les droits pour réaliser cette action');
        $connection=New Adeweb();
        $connection->connect();
        $projects=$connection->getProject(null, 4);
        $projetUnistra=Adeweb::retrieveProjectUnistra($projects, "Unistra");
        $connection->setProject($projetUnistra->attributes()->id);
        $retour=$connection->getEvent(['resources', $ressource], ['startDate', $datedebut], ['endDate', $datefin], ['detail', $detail]);
        return $retour;
    }

    static public function retrieveIdAdeProf(){

        $u=AuthService::getUser();
        if (!AuthService::isAdmin()) throw new NotAllowedException('Vous n\'avez pas les droits pour réaliser cette action');
        $connection=New Adeweb();
        $connection->connect();
        $projects=$connection->getProject(null, 4);
        $projetUnistra=Adeweb::retrieveProjectUnistra($projects, "Unistra");
        $connection->setProject($projetUnistra->attributes()->id);

        $listeProf=$connection->getRessources(['category', 'instructor'], ['detail', '7']);


        foreach ($listeProf as $prof){
            $ressourceADE=new RessourceADE();
            $ressourceADE->setNom($prof->attributes()->name);
            $ressourceADE->setEmail($prof->attributes()->email);
            $ressourceADE->setId($prof->attributes()->id);
            $r=RessourceADE::getById($ressourceADE->getId());
            if (is_null($r)){
                $ressourceADE->i();
            }else{
                $ressourceADE->u(['id', $ressourceADE->getId()]);
            }

            $user=Utilisateur::getByEmail($prof->attributes()->email);
            if (!is_null($user) && $user->getIdADE()!=$prof->attributes()->email){
                $user->setIdADE($prof->attributes()->id);
                $user->u(['id_user', $user->getIdUser()]);
            }else{

                $user=Utilisateur::getByNomPrenom((string) $prof->attributes()->name);

                if (!is_null($user) && is_array($user)&& count($user)==1){
                    $u=Utilisateur::getById($user[0]->getId());
                    $u->setIdADE($prof->attributes()->id);
                    $u->u(['id_user', $u->getIdUser()]);
                }


            }
        }


    }

    /** Fonction qui récupère tous les trainees d'un éta
     * @param $etablissementId
     * @return array
     */
    static public function retrieveGroupAdeEtablissement($etablissementId){
        $u=AuthService::getUser();
        if (!AuthService::canDo($u, "acceder_gestion_structure"))throw new NotAllowedException("Vous n'êtes pas autorisé à réaliser cette action");
        $connection=New Adeweb();
        $connection->connect();
        $projects=$connection->getProject(null, 4);
        $projetUnistra=Adeweb::retrieveProjectUnistra($projects, "Unistra");
        $connection->setProject($projetUnistra->attributes()->id);
        $listeGroupe=$connection->getRessources(['category', 'trainee'], ['detail', '13'],['fatherIds', $etablissementId],['tree', true]);

        $ar=explode("|", $etablissementId);
        if (is_array($ar)){
            foreach ($ar as $a){
                $folder[]=$connection->getRessources(['category', 'trainee'], ['detail', '13'],['id', $a],['tree', true]);
            }
        }else{
            $folder[]=$connection->getRessources(['category', 'trainee'], ['detail', '13'],['id', $etablissementId],['tree', true]);
        }

//        var_dump($test);
        $data[]=$listeGroupe;
        $data[]=$folder;
        return $data;
    }

    /**Fonction qui récupère les événements dans ade et les insère en bdd en les rattachants à un groupe et à 1 enseignant
     * @param $promo Promotion
     */
    static public function synchroADE($promo)
    {

        $semestre=Semestre::getByIdPromotion($promo->getId(), true);
        if (!is_array($semestre) || count($semestre)==0){
            $data['nombreevent']=0;
            $data['nombreeventupdate']=0;
            $data['nombreeventinsert']=0;
            return $data;
        }
        $dd=DateTime::createFromFormat('Y-m-j', $semestre[0]->getDateDebutSemestre());

        $df=DateTime::createFromFormat('Y-m-j', $semestre[0]->getDateFinSemestre());

        $datedebut = $dd->format('m/j/Y');
        $datefin = $df->format('m/j/Y');
        $detail = "8";
        $datedebut = '05/13/2020';
        $datefin = '05/13/2020';

        $listeEvent = Adeweb::retrieveEventPromotion($promo->getUrlade(), $datedebut, $datefin, $detail);

        $groupes = Groupe::getBySemestreOuvert($promo->getId());
              /** @var Groupe $groupe */

        $listeeventsansgroupe=[];
        $listeeventsansprof=[];
        $nombreevent=count($listeEvent);
        $nombreeventupdate=0;
        $nombreeventinsert=0;

        $marqueur=time();
        foreach ($listeEvent as $event) {
            $now = new DateTime("now");
            $appel = Appel::getByIdade($event->attributes()->id);
            $hd=DateTime::createFromFormat('H:i', (string) $event->attributes()->startHour);
            $hf=DateTime::createFromFormat('H:i', (string) $event->attributes()->endHour);
            $debut = DateTime::createFromFormat('j/m/Y H:i',(string) $event->attributes()->date . " ". (string) $event->attributes()->startHour);

            $duree = (string) $event->attributes()->duration;
            if (count($appel) == 0) {
                $a = new Appel();
                $a->setIdade($event->attributes()->id);
                $a->setFait(0);
                $a->setCreepar(0);

                $a->setMarqueur($marqueur);
            }else{
                /* $a Appel*/
                $a = $appel[0];
            }

            if ($a->getFait()==0){
                $a->setDateimport($now->format('Y-m-d h:m:s'));

                //Gérer la date et la durée et dernière date de modification
                $a->setDate($debut->format('Y-m-d'));
                $duree=$hd->diff($hf);
                $a->setDuree($duree->format("%H:%I:%S"));


                $lastmodified=DateTime::createFromFormat('m/j/Y H:i', (string) $event->attributes()->lastUpdate);
                $a->setLast_modified($lastmodified->format('Y-m-d h:m:s'));

                //Gérer le creneaux horaire
                $a = Appel::findCreneaux($a, $debut);

                //Gérer la matière
                $a->setMatiere((string) $event->attributes()->name);


                $ressources=$event->resources->resource;

                $groupetrouve=0;
                $profTrouve=0;
                foreach ($ressources as $ressource){

                    if ($ressource->attributes()->category=="instructor"){
                        $prof=Utilisateur::getByIdAde($ressource->attributes()->id);
                        if (!is_null($prof)){
                            $a->setEnseignantId($prof->getId());
                            $profTrouve=1;
                        }
                    }
                    if ($ressource->attributes()->category=="trainee"){
                        /** @var Groupe $g */
                        foreach ($groupes as $g){
                            $adeGrou=explode('|', $g->getAdenom());

                            if (is_array($adeGrou)){
                                if (in_array((string) $ressource->attributes()->name, $adeGrou)){

                                    $a->setGroupeId($g->getId());
                                    $groupetrouve=1;
                                }
                            }
                        }
                    }
                }
                if ($profTrouve==0) {
                    $listeeventsansprof[]=$event;

                }
                if ($groupetrouve==0) {
                    $listeeventsansgroupe[]=$event;
                }
            }


            if (count($appel) == 0) {
                $a->setId($a->i());
                $nombreeventinsert++;

            }else{
                $a->u(['id', $a->getId()]);
                $nombreeventupdate++;
            }

            $data['eventsansgroupe']=$listeeventsansgroupe;
            $data['eventsansprof']=$listeeventsansprof;




        } /** Fin du foreach sur liste event */



        //Suppression des événements qui ont été supprimé dans ADE.
        // On supprime les événement créé par le cron (getcreerpar==0) et avec un marqueur différent que celui
        //qui a été créé au début de la méthode
        $appels=Appel::getBySemestre($semestre->getId());

        /** @var Appel $appel */
        foreach ($appels as $appel){
            if ($appel->getMarqueur()!=$marqueur && $appel->getCreepar()==0 && $appel->getFait()==0){
                $appel->d(['id', $appel->getId()]);
            }
        }


        $data['nombreevent']=$nombreevent;
        $data['nombreeventupdate']=$nombreeventupdate;
        $data['nombreeventinsert']=$nombreeventinsert;
        return $data;
    }








    /** Renvoie l'URL du serveur ADE. Si le paramètre n'est pas rensignée, l'identifiant de session est automatiquement ajouté
     * @param bool $session
     */
    public function getUrlBase($session=true){
        $this->setUrl($this->getProtocol().$this->getServer().'/jsp/webapi?');

        if ($session){
           $this->addSessionId();
        }

    }

    /** Renvoie l'URL du serveur ADE. Si le paramètre n'est pas rensignée, l'identifiant de session est automatiquement ajouté
     * @param bool $session
     */
    public function getUrlProxyBase($session=true){

        $this->setUrl($this->getProxyUrl());

        if ($session){
           $this->addSessionId();
        }

    }

    public function addSessionId(){
        $this->setUrl($this->getUrl()."sessionId=".$this->getSessionid());
    }

    public function addFunction($name, $first=false){
        if ($first){
            $this->setUrl($this->getUrl()."function=".$name);
        }else{
            $this->setUrl($this->getUrl()."&"."function=".$name);
        }
    }

    public function addElementUrl($name, $value, $first=false){
        if ($first){
            $this->setUrl($this->getUrl().$name."=".$value);
        }else{
            $this->setUrl($this->getUrl()."&".$name."=".$value);
        }
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProxyLogin()
    {
        return $this->proxy_login;
    }

    /**
     * @param mixed $proxy_login
     */
    public function setProxyLogin($proxy_login)
    {
        $this->proxy_login = $proxy_login;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProxyMdp()
    {
        return $this->proxy_mdp;
    }

    /**
     * @param mixed $proxy_mdp
     */
    public function setProxyMdp($proxy_mdp)
    {
        $this->proxy_mdp = $proxy_mdp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param mixed $server
     */
    public function setServer($server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getSessionid()
    {
        return $this->sessionid;
    }

    /**
     * @param mixed $sessionid
     */
    public function setSessionid($sessionid)
    {
        $this->sessionid = $sessionid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProxyUrl()
    {
        return $this->proxy_url;
    }

    /**
     * @param mixed $url
     */
    public function setProxyUrl($url)
    {
        $this->proxy_url = $url;
        return $this;
    }




}
