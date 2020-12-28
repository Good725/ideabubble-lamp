<?php


namespace Ideabubble\Timeoff\Entity;


class Note
{
    private $id;
    private $requestId;
    private $userId;
    private $createdAt;
    private $content;
    
    
    public function __construct($id, $requestId, $userId, $content)
    {
        $this->id = $id;
        $this->requestId = $requestId;
        $this->userId = $userId;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->content = $content;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
    
    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
    
}