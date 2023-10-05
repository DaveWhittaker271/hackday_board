<?php

namespace core\entity;

class Comment
{
    public int $id;

    public int $user_id;

    public int $idea_id;

    public string $text;

    public int $timecreated;
}