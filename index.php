<?php
/**
 * @author        Dennis Rogers
 * @address        www.drogers.net
 */
?>
<?php require('includes/header.phtml'); ?>
<div data-role="page" data-theme="b">
	<div data-role="header">
		<h1>Character Energy Data for User <?php echo $bdo->getUser() ?></h1>
	</div><!-- /header -->
    <div data-role="content">
        <form action="/" method="post" data-ajax="false" id="character-form">
            <div class="ui-grid-d">
                <div class="ui-block-a"><span class="ui-btn">Name</span></div>
                <div class="ui-block-b"><span class="ui-btn">State</span></div>
                <div class="ui-block-c"><span class="ui-btn">Energy</span></div>
                <div class="ui-block-d"><span class="ui-btn">Time</span></div>
                <div class="ui-block-e"><span class="ui-btn">Update</span></div>
                <?php foreach($bdo->getCharacters() as $character): ?>
                    <div class="ui-block-a name"><span class="ui-btn" id="name_<?php echo $character->id ?>"><?php echo $character->name; ?></span></div>
                    <div class="ui-block-b">
                        <select id="state_<?php echo $character->id ?>" name="state[<?php echo $character->id ?>]">
                            <option <?php if($character->state == BDO_App::ONLINE) echo 'selected="selected"' ?> value="<?php echo BDO_App::ONLINE ?>">Logged In</option>
                            <option <?php if($character->state == BDO_App::OFFLINE) echo 'selected="selected"' ?> value="<?php echo BDO_App::OFFLINE ?>">Offline</option>
                            <option <?php if($character->state == BDO_App::BED) echo 'selected="selected"' ?> value="<?php echo BDO_App::BED ?>">In Bed</option>
                            <option <?php if($character->state == BDO_App::CASHBED) echo 'selected="selected"' ?> value="<?php echo BDO_App::CASHBED ?>">In Cash Shop Bed</option>
                        </select>
                    </div>
                    <div class="ui-block-c"><input type="text" readonly disabled id="energy_<?php echo $character->id ?>" value="<?php echo $character->energy ?>" /></div>
                    <div class="ui-block-d"><input type="text" readonly disabled value="<?php echo $character->time ?>" /></div>
                    <div class="ui-block-e"><input type="text" name="energy[<?php echo $character->id ?>]" /></div>
                <?php endforeach; ?>
            </div>
            <div class="ui-grid-c">
                <div class="ui-block-a">&nbsp;</div>
                <div class="ui-block-b">&nbsp;</div>
                <div class="ui-block-c"><span class="ui-btn">Max Energy:</span> </div>
                <div class="ui-block-d">
                    <input type="text" id="max" name="max" placeholder="Max Energy" value="<?php echo $bdo->getMax() ?>" />
                </div>
            </div>
            <input type="text" id="new" name="new" placeholder="Add Character" />
            <button id="submit" type="submit">Update</button>
        </form>
    </div><!-- /content -->
</div>
<?php require('includes/footer.phtml'); ?>