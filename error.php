<?php if ($_REQUEST['javascriptDisabled']) { ?>
            <div class="errorDiv">
            	<h2 class="errorText"> JavaScript required. </h1>
                <p class="errorText"> Please enable it and reload the page. </p>
            </div>
<?php } else if ($_REQUEST['cookiesDisabled']) { ?>
            <div class="errorDiv">
            	<h2 class="errorText"> Cookies disabled. </h1>
                <p class="errorText"> Please enable them and reload the page. </p>
            </div>
<?php }?>