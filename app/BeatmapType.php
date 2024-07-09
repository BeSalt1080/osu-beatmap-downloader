<?php

namespace App;

enum BeatmapType : String
{
    case Favourite = 'favourite';
    case Graveyard = 'graveyard';
    case Guest = 'guest';
    case Loved = 'loved';
    case MostPlayed = 'most_played';
    case Nominated = 'nominated';
    case Pending = 'pending';
    case Ranked = 'ranked';
}
