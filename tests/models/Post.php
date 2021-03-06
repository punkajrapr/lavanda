<?php

namespace App;

use Carbon\Carbon;
use Idealogica\Lavanda\Model;
use Idealogica\Lavanda\Descriptor\Descriptor;
use Idealogica\Lavanda\Descriptor\SortDescriptor;
use Idealogica\Lavanda\Descriptor\StorageDescriptor;
use Idealogica\Lavanda\Descriptor\PresentationDescriptor;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    protected $table = 'lv_posts';

    public static function getItemsPerPage()
    {
        return 5;
    }

    public static function buildActionsDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('create')->
            add('edit')->
            add('destroy');
    }

    public static function buildStorageDescriptor(StorageDescriptor $descriptor)
    {
        $descriptor->
            add('image', 'image', [
                'path' => 'image/post',
                'type' => 'jpg']);
    }

    public static function buildListDescriptor(PresentationDescriptor $descriptor)
    {
        $descriptor->
            add('id', 'text', '#', ['width' => '50px'])->
            add('created_at', 'text', 'Date', ['width' => '120px'])->
            add('title', 'text', 'Title', ['max_len' => 100])->
            add('image', 'image', 'Image', ['width' => '140px', 'img_width' => 100]);
    }

    public static function buildItemDescriptor(PresentationDescriptor $descriptor)
    {
        $descriptor->
            add('id', 'text', '#')->
            add('created_at', 'text', 'Date')->
            add('title', 'text', 'Title')->
            add('image', 'image', 'Image', [
                'img_width' => 600])->
            add('body', 'text', 'Text');
    }

    public static function buildSearchDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('id')->
            add('title')->
            add('body');
    }

    public static function buildSortDescriptor(SortDescriptor $descriptor)
    {
        $descriptor->
            add('id', '#')->
            add('created_at', 'Date')->
            add('title', 'Title');
    }

    public static function buildDeleteDescriptor(Descriptor $descriptor)
    {
        $descriptor->
            add('comments')->
            add('tags');
    }

    public static function buildFormQuery(Builder $query)
    {
        $query->with('comments')->with('tags');
    }

    public static function buildForm(Form $form, $config)
    {
        $form->
            add('created_at', 'date', [
                'label' => 'Date',
                'rules' => 'required|date',
                'required' => true,
                'default_value' => Carbon::now()->format('y-m-d')])->
            add('title', 'text', [
                'label' => 'Post title',
                'rules' => 'required|min:5',
                'required' => true])->
            add('body', 'textarea', [
                'label' => 'Post text',
                'rules' => 'required|max:5000|min:5',
                'required' => true])->
            add('image', 'image', [
                'label' => 'Image',
                'rules' => 'required|lavanda_image:jpeg,gif,png',
                'required' => true])->
            add('tags', 'lookup', [
                'model' => 'App\Tag',
                'property' => 'text',
                'label' => 'Tags'])->
            add('comments', 'rowset', [
                'model' => 'App\Comment',
                'label' => 'Comments',
                'row_label' => 'Comment']);
    }

    /**
     * Get the comments for the blog post.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function tags()
    {
         return $this->belongsToMany('App\Tag', 'lv_post_tag');
    }
}
