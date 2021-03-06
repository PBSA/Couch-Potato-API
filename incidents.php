<?php

    function make_incident($game, $config){

        $now = gmdate("Y-m-d") . 'T' . gmdate("H:i:s");
        $timestamp = $now . 'Z';      
        $pushed = $now . '.000Z';    
        $unique_string = unique_string($game->start_time, $game);      
        $incident = new stdClass();
        $incident->call = $game->call;
        $incident->unique_string = $unique_string;
        $incident->timestamp = $timestamp;

        // arguments change according to the call type.
        $arguments = new stdClass();
        if($game->call == 'create'){
            $arguments->season = '2019-2020';
        }
        elseif($game->call == 'in_progress'){
            $arguments->whistle_start_time = $game->whistle_start_time;
        }
        elseif($game->call == 'finish'){
            $arguments->whistle_end_time = $game->whistle_end_time;
        }
        elseif($game->call == 'result'){
            $arguments->home_score = $game->home_score;
            $arguments->away_score = $game->away_score;
        }

        $id = new stdClass();
        $id->home = $game->home;
        $id->away = $game->away;
        $id->sport = $game->sport;
        $id->start_time = $game->start_time;
        $id->event_group_name = $game->league;

        $provider_info = new stdClass();
        //$provider_info->match_id = $game->match_id;
        
        $provider_info->name = $config->providers->name;
        //$provider_info->source = 'direct string input';
        //$provider_info->source_file = '';
        $provider_info->pushed = $pushed;

        $incident->arguments = $arguments;
        $incident->id = $id;
        $incident->provider_info = $provider_info;
    
        return $incident;
    }

    function unique_string($starttime, $game){
        // create a unique incident identifier
        $uniquestring = str_replace(':', '', $starttime);
        $uniquestring .= '__';
        $uniquestring .= $game->sport . "__" . $game->league . "__";
        $uniquestring .= $game->home . "__" . $game->away . "__";
        $uniquestring .= $game->call . '__20192020';
        $uniquestring = str_replace(' ', '-', $uniquestring);
        return strtolower($uniquestring);
    }
    
    function format_text($string){
        // replace space with underscores
        return str_replace(' ', '_', $string);
    }
  
?>
