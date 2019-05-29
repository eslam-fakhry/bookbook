<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;


class User extends \TCG\Voyager\Models\User
{
    use Notifiable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','bio','avatar', 'password', 'provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'provider_name', 'provider_id', 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reported()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function languages()
    {
        return $this->morphToMany(Language::class, 'languageable')
            ->withPivot('is_primary');
    }

    public function nativeLanguage()
    {

        return $this
            ->morphToMany(Language::class, 'languageable')
            ->withPivot('is_primary')
            ->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function postedBooks()
    {
        return $this->hasMany(Book::class, 'poster_id');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function readList()
    {
        return $this->belongsToMany(Review::class, 'bookmarks')->withTimestamps();
    }

    public function scopeFeed()
    {
        $userIds = $this->followings()->pluck('followed_id');
        $userIds[] = $this->{'id'};
        return Review::whereIn('reviewer_id', $userIds)->latest();
    }

    public function getControlsJsonAttribute()
    {
        $controlsJson = [];

        $controlsJson['followers'] = count($this->{'followers'});
        $controlsJson['following'] = count($this->{'followings'});
        $controlsJson['reviews'] = count($this->{'reviews'});

        // urls
        $controlsJson['url'] = route('user.profile', $this->{'id'});
        $controlsJson['followersUrl'] = route('followers');
        $controlsJson['followingUrl'] = route('following');
        $controlsJson['reportUrl'] = route('user.report', $this->{'id'});

        return json_encode($controlsJson);
    }

    public function getFollowedAttribute(){
        return $this->{'followers'}->contains(\Auth::user());
    }

//    public function setPhotoAttribute(string $path){
//        $this->{'photo'} = $path;
//    }

    public function getAvatarUrlAttribute(){
        if( \Str::startsWith($this->{'avatar'} ,'http')){
            return $this->{'avatar'};
        }
        return \Storage::url($this->{'avatar'});
    }


//    public function delete()
//    {
//
//    }


}
