<?php

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

use sammaye\mongoyii\Document;

/**
 * Represents the article itself, and all of its data
 */
class Article extends Document
{
	// We are going to predefine the schema here
	public $userId; // The user id, creator
	public $title; // The article title
	public $body; // The article content

	/**
	 * This will contain a list of users to committed revisions to this article
	 * including the time of the revision
	 * @var array
	 */
	public $revisions=array();
	public $references=array();

	public $likes=array();
	public $dislikes=array();

	public $views=0;
	public $totalComments=0; // Pre-aggregated sum of total comments
	
	public $testemc = array();

	public function defaultScope()
	{
		return array(
			// Don't allow downvoted articles to show by default
			// Uncomment this line to get the default scope to work
			// 'condition' => array('dislikes' => array())
		);
	}

	public function collectionName()
	{
		return 'article';
	}

	public function behaviors()
	{
	  return array(
  		'EMongoTimestampBehavior' => array(
  			'class' => 'sammaye\mongoyii\behaviors\TimestampBehavior' // adds a nice create_time and update_time Mongodate to our docs
  	  ));
	}

	public function relations()
	{
		return array(
			'author' => array('one','User','_id','on'=>'userId'),
			'comments' => array('many','Comment','on' => '_id','articleId'),

			// Here we define the likes/dislikes relationships
			'usersLiked' => array('many', 'User', '_id','on'=>'likes'),
			'usersDisliked' => array('many', 'User','_id','on'=>'dislikes')
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return array(
			array('title,body','required'),
			array('title','length','max'=>255),
			array('body','length','max'=>2500),
				
			//array(''),

			array('title,body', 'safe', 'on' => 'search') // search by title
		);
	}

	public function beforeSave()
	{
		if($this->userId===null) $this->userId = new ObjectID(Yii::app()->user->id); // If the user id is null we just take what is in session

		if(!$this->getIsNewRecord()){
			// If this is not a new recrd then it is being edited
			// Lets form a subdocument for revisions and place it in
			$revision = array(
				'userId' => empty(Yii::app()->user->id) ? Yii::app()->request->getUserHostAddress() : new ObjectID(Yii::app()->user->id),
				'time' => new UTCDateTime(time()*1000)
			);
			$this->revisions[]=$revision;
		}
		return parent::beforeSave();
	}

	public function afterSave()
	{
		if($this->getIsNewRecord()){
			//$this->author->saveCounters(array('totalArticles'=>1)); // Inc the amount of articles the user has
		}
		return parent::afterSave();
	}

	public function afterDelete()
	{
		if($this->author->totalArticles>1)
			$this->author->saveCounters(array('totalArticles'=>-1)); // deinc the amount of articles the user has
		else{
			$this->author->totalArticles=0; // $inc won't work with 0...I should think of a decent way to fix that...
			$this->author->save();
		}
		Comment::model()->deleteAll(array('articleId'=>new ObjectID($this->_id))); // Lets rid ourselves of those troll comments
		return parent::afterDelete();
	}

	/**
	 * When we like or dislike an article it creates two embedded documents. Notice how I am doing this completely atomically?
	 * I am merely using MongoYii as a wrapper to process the command. This is how subdocuments work. MongoYii will not get in the way
	 * and it is upto you, the developer, to code your embedded relationships.
	 *
	 * The next thing about this is to show a list of users who liked and dislike this article. This is easy since the getRelated() function
	 * actually supports resolving a list of ObjectIds using the $in operator. All you need to do is specify that the relation is on the "likes" or "dislikes"
	 * field, kool eh?
	 */
	public function like()
	{
		$this->updateAll(array('_id'=>$this->_id), array(
			'$pull' => array('dislikes'=> new ObjectID(Yii::app()->user->id)),
			'$addToSet' => array('likes' => new ObjectID(Yii::app()->user->id))
		));
		$this->refresh(); // Probably not needed, would only be needed if you want to show the new like/dislike count in the response
	}

	public function dislike()
	{
		$this->updateAll(array('_id'=>$this->_id), array(
			'$pull' => array('likes'=> new ObjectID(Yii::app()->user->id)),
			'$addToSet' => array('dislikes' => new ObjectID(Yii::app()->user->id))
		));
		$this->refresh();
	}

	/**
	 * Gets the listing content preview. Used on the search and other listings to get an abstract of the body for use there.
	 * @param int $count
	 * @return string
	 */
	public function getBodyPreview($count=250)
	{
		if(strlen($this->body)>$count)
			return substr($this->body, 0, $count-3).'...';
		else
			return $this->body;
	}
}