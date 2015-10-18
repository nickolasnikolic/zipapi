<?php

class Zip{

    function distance($zip1,$zip2){
        $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

        $server = $url["host"];
        $user = $url["user"];
        $pass = $url["pass"];
        $database = substr($url["path"], 1);

        $db = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $pass);
        $stmt = $db->prepare('SELECT * FROM zip WHERE zip = :zip1;');

        $stmt->bindParam( ':zip1', $zip1 );

        $stmt->execute();

        $result= $stmt->fetchAll(PDO::FETCH_ASSOC);

        $lat1 = $result["latitude"];
        $lon1 = $result["longitude"];

        $stmt= $db->prepare('SELECT * FROM zip WHERE zip = :zip2;');

        $stmt->bindParam( ':zip2', $zip2 );

        $stmt->execute();

        $result= $stmt->fetchAll(PDO::FETCH_ASSOC);


        $lat2 = $result['latitude'];
        $lon2 = $result['longitude'];

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

        $resultCoords = $stmtCoords->fetchAll(PDO::FETCH_ASSOC);

        $lat = $resultCoords["latitude"];
        $lon = $resultCoords["longitude"];

        $stmtCoords= $db->prepare('SELECT zip
                                    FROM zipcodes
                                    WHERE ( POW( ( 69.1 * ( lon - :lon ) * cos( :lat / 57.3 ) ) ,2 ) +
                                          POW( ( 69.1 * ( lat - :lat ) ),2 ) ) < ( :radius * :radius );');


        $stmtCoords->bindParam( ':lon', $lon );
        $stmtCoords->bindParam( ':lat', $lat );
        $stmtCoords->bindParam( ':radius', $radius );

        $stmtCoords->execute();

        $zipcodes = $stmtCoords->fetchAll(PDO::FETCH_ASSOC);

        return $zipcodes;
    }

}
