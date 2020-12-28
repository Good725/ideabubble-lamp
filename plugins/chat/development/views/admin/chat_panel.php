<div id="chat-panel-collapsed" class="<?=Auth::instance()->has_access('chat') ? '' : 'hidden'?>">
    <button type="button" class="chat-panel-toggle active">
        <span class="icon-comments"></span>
    </button>
</div>

<div id="chat-panel" class="hidden" data-assets-dir="<?=URL::get_engine_plugin_assets_base('chat')?>">
    <div class="overlayer"></div>
    <div class="textnote-header">
            <span class="textnote-header-icon">
			<i class="icon icon-comments" aria-hidden="true"></i> 
		</span>

		<span class="textnote-user">
			<span class="username"><?= $logged_in_user['name'];?> <?= $logged_in_user['surname'];?></span>
			<img class="textnote-user-avatar" src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="Profile: <?= $logged_in_user['name'] ?>" title="Profile: <?= $logged_in_user['name'] ?>" width="50" height="50" />
		</span>
		<button class="pclose"><span class="icon-times"></span></button>
	</div>

    <div class="search">
        <input type="text" placeholder="Search User" />
         <i class="glyph-icon flaticon-search"></i>
    </div>

    <div class="users">
        <div class="user template">
            <div class="send_message">
				<div class="avatar">
					<div class="status"></div>
					<div class="image"><img src="" width="50" height="50" /> </div>
                    <div class="details"></div>
				</div>
			</div>
        </div>
    </div>

    <div class="chat-rooms"></div>

    <div class="chat-room template">
        <div class="header">
            <h2></h2>
            <ul>
                <!-- <li><a href="#"><span class="glyph-icon flaticon-star"></span></a></li>-->
                <li><a class="close"><i class="icon icon-long-arrow-right" aria-hidden="true"></i></a></li>
            </ul>
        </div>
        <div class="body">
            <div class="message template" tabindex="0">
                <div class="user">
                    <div class="avatar"><img  /></div>
                    <div class="time"></div>
                </div>
                <div class="text"><p></p></div>
            </div>
        </div>
        <div class="status"></div>
        <div class="footer">
            <div class="middle-box"><input type="text" class="message" value="" placeholder="Type your message..." /></div>
            <div class="right-section">
                <a class="sending-icon">
                    <i class="glyph-icon flaticon-smile"></i>
                    <ul>
                        <li class="smiley" data-code="O:-)" data-icon="angel"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/angel.png" /></li>
                        <li class="smiley" data-code=":@" data-icon="angry"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/angry.png" /></li>
                        <li class="smiley" data-code=":triumph:" data-icon="angry-1"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/angry-1.png" /></li>
                        <li class="smiley" data-code="-_-" data-icon="bored"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/bored.png" /></li>
                        <li class="smiley" data-code="o.O" data-icon="confused"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/confused.png" /></li>
                        <li class="smiley" data-code="8=)" data-icon="cool"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/cool.png" /></li>
                        <li class="smiley" data-code="B|" data-icon="cool-1"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/cool-1.png" /></li>
                        <li class="smiley" data-code=";(" data-icon="crying"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/crying.png" /></li>
                        <li class="smiley" data-code=":cry:" data-icon="crying"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/crying-2.png" /></li>
                        <li class="smiley" data-code=":cute:" data-icon="cute"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/cute.png" /></li>
                        <li class="smiley" data-code=":-$" data-icon="embarrassed"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/embarrassed.png" /></li>
                        <li class="smiley" data-code=":emoji:" data-icon="emoji"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/emoji.png" /></li>
                        <li class="smiley" data-code=":greed:" data-icon="greed"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/greed.png" /></li>
                        <li class="smiley" data-code="=)" data-icon="happy"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/happy.png" /></li>
                        <li class="smiley" data-code=":-)" data-icon="happy-1"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/happy-1.png" /></li>
                        <li class="smiley" data-code=":)" data-icon="happy-2"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/happy-2.png" /></li>
                        <li class="smiley" data-code="^-^" data-icon="happy-3"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/happy-3.png" /></li>
                        <li class="smiley" data-code="<3" data-icon="in-love"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/in-love.png" /></li>
                        <li class="smiley" data-code=":*" data-icon="kiss"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/kiss.png" /></li>
                        <li class="smiley" data-code=":D" data-icon="laughing"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/laughing.png" /></li>
                        <li class="smiley" data-code=":mute:" data-icon="mute"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/muted.png" /></li>
                        <li class="smiley" data-code="B)" data-icon="nerd"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/nerd.png" /></li>
                        <li class="smiley" data-code=":(" data-icon="sad"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/sad.png" /></li>
                        <li class="smiley" data-code=":fearful:" data-icon="scare"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/scare.png" /></li>
                        <li class="smiley" data-code=":serious:" data-icon="scare"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/serious.png" /></li>
                        <li class="smiley" data-code=":O" data-icon="shocked"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/shocked.png" /></li>
                        <li class="smiley" data-code=":-&" data-icon="sick"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/sick.png" /></li>
                        <li class="smiley" data-code="|-)" data-icon="sleepy"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/sleepy.png" /></li>
                        <li class="smiley" data-code=":smart:" data-icon="smart"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/smart.png" /></li>
                        <li class="smiley" data-code="(@@)" data-icon="suspicious"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/suspicious.png" /></li>
                        <li class="smiley" data-code=":P" data-icon="tongue"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/tongue.png" /></li>
                        <li class="smiley" data-code=":vain:" data-icon="vain"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/vain.png" /></li>
                        <li class="smiley" data-code=";)" data-icon="wink"><img src="<?=URL::get_engine_plugin_assets_base('chat')?>/images/simileys/wink.png" /></li>
                    </ul>
                </a>
            <button type="button" class="send"><i class="glyph-icon flaticon-send-button"></i></button></div>
        </div>
    </div>

</div>

