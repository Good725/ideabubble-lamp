<?php
class IMAP
{
	public $host;
	public $port = 993;
	public $username;
	public $password;
	public $charset = 'UTF-8';
	public $secure = 'tls';
	public $pop3 = false;
	
	protected $inbox;
	
	public function connect()
	{
		try {
			//echo '{' . $this->host . ':' . $this->port . ( $this->secure ? '/ssl/novalidate-cert' : '' ) . '}INBOX' .":". $this->username.":". $this->password;
			$inbox = imap_open('{' . $this->host . ':' . $this->port . '/' . ($this->pop3 ? 'pop3' : 'imap') . '/norsh' . ($this->secure ? '/' . strtolower($this->secure) . '/novalidate-cert' : '') . '}INBOX',
					$this->username, $this->password);
			if ($inbox) {
				$this->inbox = $inbox;
				return true;
			} else {
				return false;
			}
		} catch (Exception $exc) {
			return false;
		}
	}
	
	public function close()
	{
		if( $this->inbox ){
			imap_close( $this->inbox );
			$this->inbox = null;
		}
	}

	public function info()
	{
		$info = imap_mailboxmsginfo( $this->inbox );
		return $info;
	}

	public function getNewMailIds()
	{
		if( $this->inbox ){
			$mailIds = imap_search( $this->inbox, 'UNSEEN' );
			
			return $mailIds;
		} else {
			return false;
		}
	}
	
	public function getAllMailIds()
	{
		if( $this->inbox ){
			$mailIds = imap_search( $this->inbox, 'ALL' );
			
			return $mailIds;
		} else {
			return false;
		}
	}
	
	public function markAsRead( $mailId, $read = true )
	{
		if( $this->inbox ){
			if( $read ){
				return imap_setflag_full( $this->inbox, "$mailId", "\\Seen" );
			} else {
				return imap_clearflag_full( $this->inbox, "$mailId", "\\Seen" );
			}
		} else {
			return false;
		}
	}
	
	public function getNewMails()
	{
		if( $this->inbox ){
			$mailIds = imap_search( $this->inbox, 'UNSEEN' );
			
			$mails = array();
			foreach( $mailIds as $mailId ){
				$mail = $this->getMail( $mailId );
				$mails[] = $mail;
			}
			echo count( $mailIds ) . "\n";
			print_r( $mails );
		} else {
			return false;
		}
	}
	
	public function getMail( $mailId )
	{
		$mail = array();
		$header = imap_headerinfo( $this->inbox, $mailId );
		foreach( $header->from as $i => $from ){
			if( isset( $from->personal ) ){
				$personal = imap_mime_header_decode( $from->personal );
				$from->personal = mb_convert_encoding( $personal[0]->text, $this->charset, $personal[0]->charset == 'default' ? 'iso-8859-1' : $personal[0]->charset );
			}
		}
		if( isset( $header->reply_to ) )
		foreach( $header->reply_to as $i => $reply_to ){
			if( isset( $reply_to->personal ) ){
				$personal = imap_mime_header_decode( $reply_to->personal );
				$reply_to->personal = mb_convert_encoding( $personal[0]->text, $this->charset, $personal[0]->charset == 'default' ? 'iso-8859-1' : $personal[0]->charset );
			}
		}
		
		if( isset( $header->sender ) )
		foreach( $header->sender as $i => $sender ){
			if( isset( $sender->personal ) ){
				$personal = imap_mime_header_decode( $sender->personal );
				$sender->personal = mb_convert_encoding( $personal[0]->text, $this->charset, $personal[0]->charset == 'default' ? 'iso-8859-1' : $personal[0]->charset );
			}
		}
		//print_r( $header );
		$mail['header'] = $header;
		//$overview = imap_fetch_overview( $this->inbox, $mailId, 0 );
				
		$structure = imap_fetchstructure( $this->inbox, $mailId );
		$mail['content'] = $this->getMailContent( $mailId );
		return $mail;
	}
	
	protected function getMailContent( $mailId )
	{
		$structure = imap_fetchstructure( $this->inbox, $mailId );
		
		//echo '<!-- '.print_r($y,true).' -->';
		
		$content = array();
		$content['attachments'] = array();
		
		if( $structure->type == 0 ){
			if( $structure->subtype == 'PLAIN' ){
				$cset = $this->getCharset( $structure->parameters );
				$t = imap_fetchbody( $this->inbox, $mailId, 1, FT_PEEK );
				if( $structure->encoding == 4 ){
					$t = imap_qprint( $t );
				} else if( $structure->encoding == 3 ){
					$t = base64_decode( $t );
				}
				
				if( $cset == "UTF-8" ){
					$content["text"] = $t;
				} else {
					$content["text"] = mb_convert_encoding( $t, "UTF-8", $cset );
				}
				$content["html"] = null;
			} else if( $structure->subtype == "HTML" ){
				$cset = $this->getCharset( $structure->parameters );
				$t = imap_fetchbody( $this->inbox, $mailId, 1, FT_PEEK );
				if( $structure->encoding == 4 ){
					$t = imap_qprint( $t );
				} else if( $structure->encoding == 3 ){
					$t = base64_decode( $t );
				}
				
				if( $cset == "UTF-8" ){
					$content["html"] = $t;
				} else {
					$t = $this->changeHTMLCharset( $t, "UTF-8" );
					$content["html"] = mb_convert_encoding( $t, "UTF-8", $cset );
				}
				$content["text"] = null;
			} else {
				trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ), E_USER_WARNING );
			}
		}
		else if( $structure->type ==  1)
		{
			$pi=1;
			foreach( $structure->parts as $p ){
				if( $p->type == 0 && !( isset( $p->disposition ) && ( $p->disposition == "attachment" || ( $p->disposition == "inline" && isset( $p->dparameters ) && $this->attp( $p->dparameters, "filename"  )) ) ) ){
					if( $p->subtype == "PLAIN" ){
						$cset = $this->getCharset( $p->parameters );
						$t = imap_fetchbody( $this->inbox, $mailId, $pi, FT_PEEK );
						if( $p->encoding == 4 ){
							$t = imap_qprint( $t );
						} else if( $p->encoding == 3 ){
							$t = base64_decode( $t );
						}
						
						if($cset=="UTF-8"){
							$content["text"]=$t;
						} else {
							$content["text"] = mb_convert_encoding( $t, "UTF-8", $cset );
						}
					} else if( $p->subtype == "HTML" ){
						$cset = $this->getCharset( $p->parameters );
						$t = imap_fetchbody( $this->inbox, $mailId, $pi, FT_PEEK );
						if( $p->encoding == 4 ){
							$t = imap_qprint( $t );
						} else if( $p->encoding == 3 ){
							$t = base64_decode( $t );
						}
						
						if($cset=="UTF-8"){
							$content["html"]=$t;
						} else {
							$t = $this->changeHTMLCharset( $t, "UTF-8" );
							$content["html"] = mb_convert_encoding( $t, "UTF-8", $cset );
						}
					} else {
						trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi", E_USER_WARNING );
					}
				} else if( $p->type == 1 ){
					$ppi = 1;
					foreach( $p->parts as $pp )
					{
						if( $pp->type==0 && !( isset( $pp->disposition ) && ( $pp->disposition == "attachment" || ( $pp->disposition == "inline" && isset( $pp->dparameters ) && $this->attp( $pp->dparameters, "filename" ) ) ) ) ){
							if( $pp->subtype == "PLAIN" ){
								$cset = $this->getCharset( $pp->parameters );
								$t = imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi, FT_PEEK );
								if( $pp->encoding == 4 ){
									$t = imap_qprint( $t );
								} else if( $pp->encoding == 3 ){
									$t = base64_decode( $t );
								}
								
								if($cset=="UTF-8"){
									$content["text"]=$t;
								} else {
									$content["text"] = mb_convert_encoding( $t, "UTF-8", $cset );
								}
							} else if( $pp->subtype == "HTML" ){
								$cset = $this->getCharset( $pp->parameters );
								$t = imap_fetchbody( $this->inbox, $mailId, $pi ."." . $ppi, FT_PEEK );
								if( $pp->encoding == 4 ){
									$t = imap_qprint( $t );
								} else if($pp->encoding==3){
									$t = base64_decode( $t );
								}
								
								if($cset=="UTF-8"){
									$content["html"] = $t;
								} else {
									$t = $this->changeHTMLCharset( $t, "UTF-8" );
									$content["html"] = mb_convert_encoding( $t, "UTF-8", $cset );
								}
							} else {
								trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi:$ppi", E_USER_WARNING );
							}
						} else if( $pp->type == 1 ){

							$pppi = 1;
							foreach( $pp->parts as $ppp )
							{
								if( $ppp->type==0 && !( isset( $ppp->disposition ) && ( $ppp->disposition == "attachment" || ( $ppp->disposition == "inline" && isset( $ppp->dparameters ) && $this->attp( $ppp->dparameters, "filename" ) ) ) ) ){
									if( $ppp->subtype == "PLAIN" ){
										$cset = $this->getCharset( $ppp->parameters );
										$t = imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi ."." . $pppi, FT_PEEK );
										if( $ppp->encoding == 4 ){
											$t = imap_qprint( $t );
										} else if( $ppp->encoding == 3 ){
											$t = base64_decode( $t );
										}

										if($cset=="UTF-8"){
											$content["text"]=$t;
										} else {
											$content["text"] = mb_convert_encoding( $t, "UTF-8", $cset );
										}
									} else if( $ppp->subtype == "HTML" ){
										$cset = $this->getCharset( $ppp->parameters );
										$t = imap_fetchbody( $this->inbox, $mailId, $pi ."." . $ppi . "." . $pppi, FT_PEEK );
										if( $ppp->encoding == 4 ){
											$t = imap_qprint( $t );
										} else if($ppp->encoding==3){
											$t = base64_decode( $t );
										}

										if($cset=="UTF-8"){
											$content["html"] = $t;
										} else {
											$t = $this->changeHTMLCharset( $t, "UTF-8" );
											$content["html"] = mb_convert_encoding( $t, "UTF-8", $cset );
										}
									} else {
										trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi:$ppi", E_USER_WARNING );
									}
								} else if( $ppp->type == 1 ){

								} else {
									$attachmentName = "";
									if( isset( $ppp->parameters ) && $this->attp( $ppp->parameters, "name" ) != "" ){
										$attachmentName = $this->attp( $ppp->parameters, "name" );
									} else if( isset( $pp->dparameters ) && $this->attp( $ppp->dparameters, "filename" ) != "" ){
										$attachmentName = $this->attp( $ppp->dparameters, "filename" );
									}
									$attachmentName_d = imap_mime_header_decode( $attachmentName );
									if( $attachmentName_d[0]->charset == "default" ){
										$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", "ISO-8859-1" );
									} else {
										$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", $attachmentName_d[0]->charset );
									}
									$attachment = array();
									if( $ppp->encoding == 3 ){
										$attachment["content"] = base64_decode( imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi . "." . $pppi, FT_PEEK ) );
									} else if( $pp->encoding == 4 ){
										$attachment["content"] = imap_qprint( imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi . "." . $pppi, FT_PEEK ) );
									} else if( $pp->encoding == 1 || $pp->encoding == 2 ){
										$attachment["content"] = imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi . "." . $pppi, FT_PEEK );
									} else {
										trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi:$ppi:$pppi", E_USER_WARNING );
									}
									if( $attachment["content"] ){
										$tmp = tempnam( '/tmp/', 'attachment' );
										file_put_contents( $tmp, $attachment["content"] );
										$attachment['filename'] = $attachmentName;
										$attachment["content"] = $tmp;
										if (isset($p->id)) {
											$attachment["id"] = $ppp->id;
										}
										$content["attachments"][] = $attachment;
									}
								}

								$pppi++;
							}
						} else {
							$attachmentName = "";
							if( isset( $pp->parameters ) && $this->attp( $pp->parameters, "name" ) != "" ){
								$attachmentName = $this->attp( $pp->parameters, "name" );
							} else if( isset( $pp->dparameters ) && $this->attp( $pp->dparameters, "filename" ) != "" ){
								$attachmentName = $this->attp( $pp->dparameters, "filename" );
							}
							$attachmentName_d = imap_mime_header_decode( $attachmentName );
							if( $attachmentName_d[0]->charset == "default" ){
								$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", "ISO-8859-1" );
							} else {
								$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", $attachmentName_d[0]->charset );
							}
							$attachment = array();
							if( $pp->encoding == 3 ){
								$attachment["content"] = base64_decode( imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi, FT_PEEK ) );
							} else if( $pp->encoding == 4 ){
								$attachment["content"] = imap_qprint( imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi, FT_PEEK ) );
							} else if( $pp->encoding == 1 || $pp->encoding == 2 ){
								$attachment["content"] = imap_fetchbody( $this->inbox, $mailId, $pi . "." . $ppi, FT_PEEK );
							} else {
								trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi:$ppi", E_USER_WARNING );
							}
							if( $attachment["content"] ){
								$tmp = tempnam( '/tmp/', 'attachment' );
								file_put_contents( $tmp, $attachment["content"] );
								$attachment['filename'] = $attachmentName;
								$attachment["content"] = $tmp;
								if (isset($pp->id)) {
									$attachment["id"] = $pp->id;
								}
								$content["attachments"][] = $attachment;
							}
						}

						$ppi++;
					}
				} else {
					$attachmentName = "";
					if( isset( $p->parameters ) && $this->attp( $p->parameters, "name" ) != "" ){
						$attachmentName = $this->attp( $p->parameters, "name" );
					} else if( isset( $p->dparameters ) && $this->attp( $p->dparameters, "filename" ) != "" ) {
						$attachmentName = $this->attp( $p->dparameters, "filename" );
					} else {
						trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi:$ppi", E_USER_WARNING );
					}

					$attachmentName_d = imap_mime_header_decode( $attachmentName );
					if( $attachmentName_d[0]->charset == "default" ){
						$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", "ISO-8859-1" );
					} else {
						$attachmentName = mb_convert_encoding( $attachmentName_d[0]->text, "UTF-8", $attachmentName_d[0]->charset );
					}

					$attachment = array();
					if( $p->encoding == 3 ){
						$attachment["content"] = base64_decode( imap_fetchbody( $this->inbox, $mailId, $pi, FT_PEEK ) );
					} else if( $p->encoding == 4 ){
						$attachment["content"] = imap_qprint( imap_fetchbody( $this->inbox, $mailId, $pi, FT_PEEK ) );
					} else if( $p->encoding == 1 || $p->encoding == 2 ){
						$attachment[$attachmentName]["content"] = imap_fetchbody( $this->inbox, $mailId, $pi, FT_PEEK );
					} else {
						trigger_error("unable to decode email. msguid:" . imap_uid( $this->inbox, $mailId ) . ":$pi", E_USER_WARNING );
					}
					if( $attachment["content"] ){
						$tmp = tempnam( '/tmp/', 'attachment' );
						file_put_contents( $tmp, $attachment["content"] );
						$attachment['filename'] = $attachmentName;
						$attachment["content"] = $tmp;
						if (isset($p->id)) {
							$attachment["id"] = $p->id;
						}
						$content["attachments"][] = $attachment;
					}
					//print_r( $p );die();
				}
				$pi++;
			}
		}

		// clean up duplicate attachments
		$n = count ($content["attachments"]);
		for ($i = 0 ; $i < $n ; ++$i) {
			for ($j = $i + 1 ; $j < $n ; ++$j) {
				if ($content["attachments"][$i]['filename'] == $content["attachments"][$j]['filename']) {
					if (!isset($content["attachments"][$j]['id']) && isset($content["attachments"][$i]['id'])) {
						$content["attachments"][$j]['id'] = $content["attachments"][$i]['id'];
					}
					unset ($content["attachments"][$i]);
					break;
				}
			}
		}
		
		return $content;
	}
		
	private function getCharset( $params )
	{
		if( is_array( $params ) )
		{
			foreach( $params as $param )
			{
				if( strtoupper( $param->attribute ) == 'CHARSET' )
				{
					return strtoupper( $param->value );
				}
			}
		}
		return 'iso-8859-1';
	}
	
	private function changeHTMLCharset( $html, $cset = null, $di = false )
	{
		if( $cset == null )
		{
			$cseta = array();
			preg_match( "/\<meta.+content\=\\\"?.+charset\=([a-zA-Z0-9_\-]+)\\\".*\>/i", $html, $cseta );
			
			if( isset( $cseta[1] ) ){
				return $cseta[1];
			} else {
				return null;
			}
		}
		else
		{
			$cseta = array();
			preg_match( "/\<meta.+content\=\\\"?.+charset\=([a-zA-Z0-9_\-]+)\\\".*\>/i", $html, $cseta );
			
			if( isset( $cseta[1] ) ){
				$html = preg_replace( "/\<meta.+content\=\\\"?(.+)charset\=([a-zA-Z0-9_\-]+)\\\"(.*)\>/i",
										'<meta http-equiv="Content-Type" content="\1charset='.$cset.'"\3>',
										$html );
				if( $di )$html = mb_convert_encoding( $html, $cset, $cseta[1] );
			}
			return $html;
		}
	}
	
	private function attp( $a, $aa )
	{
		if( is_array( $a ) )
		{
			foreach( $a as $p )
			{
				if( strcasecmp( $p->attribute, $aa ) == 0 )
				{
					return $p->value;
				}
			}
		}
		return null;
	}
}
?>