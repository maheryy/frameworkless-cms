<?php

namespace App\Models;

use App\Core\Model;

class PageExtra extends Model
{
    private $id;
    protected $post_id;
    protected $slug;
    protected $visibility;
    protected $allow_comments;
    protected $meta_title;
    protected $meta_description;
    protected $meta_indexable;


    public function __construct()
    {
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
        $this->hydrate();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $post_id
     */
    public function setPostId($post_id): void
    {
        $this->post_id = $post_id;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $visibility
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $allow_comments
     */
    public function setAllowComments($allow_comments): void
    {
        $this->allow_comments = $allow_comments;
    }

    /**
     * @return mixed
     */
    public function getAllowComments()
    {
        return $this->allow_comments;
    }

    /**
     * @param mixed $meta_title
     */
    public function setMetaTitle($meta_title): void
    {
        $this->meta_title = $meta_title;
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    /**
     * @param mixed $meta_description
     */
    public function setMetaDescription($meta_description): void
    {
        $this->meta_description = $meta_description;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * @param mixed $meta_indexable
     */
    public function setMetaIndexable($meta_indexable): void
    {
        $this->meta_indexable = $meta_indexable;
    }

    /**
     * @return mixed
     */
    public function getMetaIndexable()
    {
        return $this->meta_indexable;
    }

}
