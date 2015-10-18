<?php

class Zip{

    function distance($zipOne,$zipTwo){
        $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

        $server = $url["host"];
        $user = $url["user"];
        $pass = $url["pass"];
        $database = substr($url["path"], 1);

        $db = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $pass);
        $stmtCoords= $db->prepare('SELECT * FROM zipcodes WHERE zip = :zip1;');

        $stmtCoords->bindParam( ':zip1', $zip1 );

        $stmtCoords->execute();

        $resultCoords = $stmtUserId->fetchAll(PDO::FETCH_ASSOC);

       $lat1 = $resultCoords["latitude"];
       $lon1 = $resultCoords["longitude"];

        $stmtCoords= $db->prepare('SELECT * FROM zipcodes WHERE zip = :zip2;');

        $stmtCoords->bindParam( ':zip2', $zip2 );

        $stmtCoords->execute();

        $resultCoords = $stmtUserId->fetchAll(PDO::FETCH_ASSOC);


        $lat2 = $resultCoords["latitude"];
        $lon2 = $resultCoords["longitude"];

       /* Convert all the degrees to radians */
       $lat1 = $this->deg_to_rad($lat1);
       $lon1 = $this->deg_to_rad($lon1);
       $lat2 = $this->deg_to_rad($lat2);
       $lon2 = $this->deg_to_rad($lon2);

       /* Find the deltas */
       $delta_lat = $lat2 - $lat1;
       $delta_lon = $lon2 - $lon1;

       /* Find the Great Circle distance */
       $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);

       $EARTH_RADIUS = 3956;
       $distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));

       return $distance;

    } // end func


    function deg_to_rad($deg){
        $radians = 0.0;
        $radians = $deg * M_PI/180.0;
        return $radians;
    }

    function zipcodesinradius($zip,$radius){
        $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

        $server = $url["host"];
        $user = $url["user"];
        $pass = $url["pass"];
        $database = substr($url["path"], 1);

        $db = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $pass);
        $stmtCoords= $db->prepare('SELECT * FROM zipcodes WHERE zip = :zip;');

        $stmtCoords->bindParam( ':zip', $zip );

        $stmtCoords->execute();

        $resultCoords = $stmtUserId->fetchAll(PDO::FETCH_ASSOC);

        $lat = $resultCoords["latitude"];
        $lon = $resultCoords["longitude"];


        $i = 0;
        $stmtCoords= $db->prepare('SELECT zip
                                    FROM zipcodes
                                    WHERE ( POW( ( 69.1 * ( lon - :lon ) * cos( :lat / 57.3 ) ) ,2 ) +
                                          POW( ( 69.1 * ( lat - :lat ) ),2 ) ) < ( :radius * :radius );');


        $stmtCoords->bindParam( ':lon', $lon );
        $stmtCoords->bindParam( ':lat', $lat );
        $stmtCoords->bindParam( ':radius', $radius );

        $stmtCoords->execute();

        $zipcodes = $stmtUserId->fetchAll(PDO::FETCH_ASSOC);

        return $zipcodes;
    }

}