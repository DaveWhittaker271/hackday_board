<?php

namespace core\entity;

class Idea
{
    public int $id;

    public int $user_id;

    public int $project_id;

    public string $title;

    public string $description;

    public int $timecreated;
}