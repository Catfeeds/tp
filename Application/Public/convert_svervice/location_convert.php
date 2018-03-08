<?php

function convert($locations) {
    $locationtmp = [];
    $pts = [];
    $rel = [];
    foreach ($locations as $location) {
        if ($location->lng != 0 && $location->lat != 0) {
            $locationtmp[] = $location;
            $pts[] = $location->lng . "," . $location->lat;
        }
        if (count($pts) == 100) {
            $tudes = convert_coords($pts);
            if (!$tudes) {
                return;
            }
            for ($i = 0; $i < 100; $i++) {
                if ($location->lng != 0 && $location->lat != 0) {
                    $locationtmp[$i]->lat = $tudes[$i]['lat'];
                    $locationtmp[$i]->lng = $tudes[$i]['lng'];
                }
            }
            $rel = array_merge($rel, $locationtmp);
            $pts = [];
            $locationtmp = [];
        }
    }
    if (count($pts) > 0) {
        $tudes = convert_coords($pts);
        if (!$tudes) {
            return;
        }
        for ($i = 0; $i < count($pts); $i++) {
            if ($location->lng != 0 && $location->lat != 0) {
                $locationtmp[$i]->lat = $tudes[$i]['lat'];
                $locationtmp[$i]->lng = $tudes[$i]['lng'];
            }
        }
        $rel = array_merge($rel, $locationtmp);
        $pts = [];
        $locationtmp = [];
    }
    return $rel;
}

?>