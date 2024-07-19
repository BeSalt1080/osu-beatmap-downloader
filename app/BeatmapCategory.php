<?php

namespace App;

enum BeatmapCategory : String
{
    case Favourite = 'favourite';
    case Ranked = 'ranked';
    case Loved = 'loved';
    case MostPlayed = 'most_played';
    case Pending = 'pending';
    case Guest = 'guest';
    case Nominated = 'nominated';
    case Graveyard = 'graveyard';
}
