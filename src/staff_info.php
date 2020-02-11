<?php
    $datainfo = file_get_contents("data.json");
    $conninfo = json_decode($datainfo);
    $conn = new mysqli($conninfo->{"host"},$conninfo->{"user"},$conninfo->{"password"},$conninfo->{"dbname"},$conninfo->{"port"});

