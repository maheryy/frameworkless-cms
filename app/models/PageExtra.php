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
    protected $seo_title;
    protected $seo_description;
    protected $seo_status;


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
     * @param mixed $seo_title
     */
    public function setSeoTitle($seo_title): void
    {
        $this->seo_title = $seo_title;
    }

    /**
     * @return mixed
     */
    public function getSeoTitle()
    {
        return $this->seo_title;
    }

    /**
     * @param mixed $seo_description
     */
    public function setSeoDescription($seo_description): void
    {
        $this->seo_description = $seo_description;
    }

    /**
     * @return mixed
     */
    public function getSeoDescription()
    {
        return $this->seo_description;
    }

    /**
     * @param mixed $seo_status
     */
    public function setSeoStatus($seo_status): void
    {
        $this->seo_status = $seo_status;
    }

    /**
     * @return mixed
     */
    public function getSeoStatus()
    {
        return $this->seo_status;
    }





}
