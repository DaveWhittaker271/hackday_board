<?php

namespace core\entity;

class File
{
    public int $id;

    public int $user_id;

    public int $project_id;

    public int $idea_id;

    public string $path;

    public int $timecreated;
}