<?php
$Alert = '无法转换此文件！ 尝试更改名称。';
$Alert1 = '无法对此文件执行病毒扫描！';
$Alert2 = '文件链接已复制到剪贴板！';
$Alert3 = '手术失败！';
$FCPlural1 = '';
if (!is_numeric($FileCount)) $FileCount = 0;
if ($FileCount === 0) $FCPlural1 = '您尚未将任何有效文件上传到 ';
else $FCPlural1 = '您已将 '.$FileCount.' 个有效文件上传到 '; 
if (!isset($ApplicationName)) $ApplicationName = 'HRConvert2'; 
if (!isset($ApplicationTitle)) $ApplicationTitle = '转换任何东西！'; 
if (!isset($CoreLoaded)) die('错误！！！ '.$ApplicationName.'-2, 此文件无法处理您的请求！ 请改为将您的文件提交到 convertCore.php！');
if (!isset($ShowFinePrint)) $ShowFinePrint = TRUE;
?>
  <body>
    <script type="text/javascript" src="Resources/jquery-3.6.0.min.js"></script>
    <div id="header-text" style="max-width:1000px; margin-left:auto; margin-right:auto; text-align:center;">
      <?php if (!isset($_GET['noGui'])) { ?><h1><?php echo $ApplicationName; ?></h1>
      <hr /><?php } ?>
      <h3>文件转换选项</h3>
      <p><?php echo $FCPlural1.$ApplicationName; ?>.</p> 
      <p>您的文件现在可以使用以下选项进行转换。</p>
    </div>

    <div id='utility' align="center">
      <p><img id='loadingCommandDiv' name='loadingCommandDiv' src='<?php echo $PacmanLoc; ?>' style="max-width:64px; max-height:64px; display:none;"/></p>
      <a id='downloadTarget' href='about:blank' style="display: none;" download></a>
    </div>

    <div id="compressAll" name="compressAll" style="max-width:1000px; margin-left: auto; margin-right: auto; text-align:center;">
      <button id="backButton" name="backButton" style="width:50px;" class="info-button" onclick="window.history.back();">&#x2190;</button>
      <button id="refreshButton" name="refreshButton" style="width:50px;" class="info-button" onclick="javascript:location.reload(true);">&#x21BB;</button>
      <br /> <br />
      <button id="scandocMoreOptionsButton" name="scandocMoreOptionsButton" class="info-button" onclick="toggle_visibility('compressAllOptions');">批量文件选项</button> 
      <div id="compressAllOptions" name="compressAllOptions" align="center" style="display:none;">
        <?php if ($AllowUserVirusScan) { ?>
        <hr style='width: 50%;' />
        <p><strong>扫描所有文件以查找病毒</strong></p>
        <p>使用 ClamAV 扫描： <input type="checkbox" id="clamscanall" value="clamscanall" name="clamScan" checked></p>
        <p>使用 ScanCore 扫描： <input type="checkbox" id="scancoreall" value="scancoreall" name="phpavScan" checked></p>
        <p><input type="submit" id="scanAllButton" name="scanAllButton" class="info-button" value='扫描所有' onclick="toggle_visibility('loadingCommandDiv');"></p>
        <script type="text/javascript">
        $(document).ready(function () {
          $('#scanAllButton').click(function() {
            var scanfiles = <?php echo json_encode($Files); ?>;
            var scanType = 'all';
            if($("input#clamscanall").is(":checked")) {
              var scanType = 'clamav'; }
            if($("input#scancoreall").is(":checked")) {
              var scanType = 'scancore'; }
            if($("input#clamscanall").is(":checked") && $("input#scancoreall").is(":checked")) {
              var scanType = 'all'; }
            $.ajax({
              type: "POST",
              url: 'convertCore.php',
              data: {
                Token1:'<?php echo $Token1; ?>',
                Token2:'<?php echo $Token2; ?>',
                scantype:scanType,
                filesToScan:scanfiles },
                success: function(ReturnData) {
                  $.ajax({
                  type: 'POST',
                  url: 'convertCore.php',
                  data: { 
                    Token1:'<?php echo $Token1; ?>',
                    Token2:'<?php echo $Token2; ?>',
                    download:'<?php echo $ConsolidatedLogFileName; ?>' },
                  success: function(returnFile) {
                    toggle_visibility('loadingCommandDiv');
                    document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'.$ConsolidatedLogFileName; ?>"; 
                    document.getElementById('downloadTarget').click(); } }); },
                  error: function(ReturnData) {
                    alert("<?php echo $Alert1; ?>"); } }); }); });
        </script>
      <?php } ?>
        <hr style='width: 50%;' />
        <p><strong>压缩和下载所有文件</strong></p>
        <p>指定文件名： <input type="text" id='userarchallfilename' name='userarchallfilename' value='HRConvert2_Files-<?php echo $Date; ?>'></p> 
        <select id='archallextension' name='archallextension'> 
          <option value="zip">文件格式</option>
          <option value="zip">Zip</option>
          <option value="rar">Rar</option>
          <option value="tar">Tar</option>
          <option value="7z">7z</option>
        </select>
        <input type="submit" id="archallSubmit" name="archallSubmit" class="info-button" value='压缩和下载' onclick="toggle_visibility('loadingCommandDiv');">
        <script type="text/javascript">
        $(document).ready(function () {
          $('#archallSubmit').click(function() { 
            var extension = document.getElementById('archallextension').value;
            if (extension === "") { 
              extension = 'zip'; } 
            $.ajax({
              type: "POST",
              url: 'convertCore.php',
              data: {
                Token1:'<?php echo $Token1; ?>',
                Token2:'<?php echo $Token2; ?>',
                archive:'1',
                filesToArchive:<?php echo json_encode($Files); ?>,
                archextension:extension,
                userfilename:document.getElementById('userarchallfilename').value },
                success: function(ReturnData) {
                  $.ajax({
                  type: 'POST',
                  url: 'convertCore.php',
                  data: { 
                    Token1:'<?php echo $Token1; ?>',
                    Token2:'<?php echo $Token2; ?>',
                    download:document.getElementById('userarchallfilename').value+'.'+extension },
                  success: function(returnFile) {
                    toggle_visibility('loadingCommandDiv');
                    document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userarchallfilename').value+'.'+extension; 
                    document.getElementById('downloadTarget').click(); } }); },
                  error: function(ReturnData) {
                    alert("<?php echo $Alert; ?>"); } }); }); });
        </script>
        <hr style='width: 50%;' />
      </div>
    </div>
    <br />
    <div style="max-width:1000px; margin-left:auto; margin-right:auto;">
      <hr />

      <?php
      foreach ($Files as $File) {
        $extension = getExtension($ConvertTempDir.'/'.$File);
        $FileNoExt = str_replace($extension, '', $File);
        if (!in_array($extension, $ConvertArray)) continue;
        $ConvertGuiCounter1++;
      ?>

      <div id="file<?php echo $ConvertGuiCounter1; ?>" name="<?php echo $ConvertGuiCounter1; ?>">
        <p href=""><strong><?php echo $ConvertGuiCounter1; ?>.</strong> <u><?php echo $File; ?></u></p>    
        <div id="buttonDiv<?php echo $ConvertGuiCounter1; ?>" name="buttonDiv<?php echo $ConvertGuiCounter1; ?>" style="height:25px;">
          
          <img id="downloadfilebutton<?php echo $ConvertGuiCounter1; ?>" name="downloadfilebutton<?php echo $ConvertGuiCounter1; ?>" src="Resources/download.png" style="float:left; display:block;" onclick="toggle_visibility('loadingCommandDiv');"/>
          <script type="text/javascript">
          $(document).ready(function () {
            $('#downloadfilebutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
              type: 'POST',
              url: 'convertCore.php',
              data: { 
                Token1:'<?php echo $Token1; ?>',
                Token2:'<?php echo $Token2; ?>',
                download:'<?php echo $File; ?>' },
              success: function(returnFile) {
                toggle_visibility('loadingCommandDiv');
                document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'.$File; ?>"; 
                document.getElementById('downloadTarget').click(); },
              error: function(ReturnData) {
                alert("<?php echo $Alert; ?>"); } }); }); });
          </script>

          <?php if ($AllowUserShare) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>
          <img id="sharefilebutton<?php echo $ConvertGuiCounter1; ?>" name="sharefilebutton<?php echo $ConvertGuiCounter1; ?>" src="Resources/link.png" style="float:left; display:block;" 
           onclick="toggle_visibility('sharefileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('sharefilebutton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('shareXfilebutton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="shareXfilebutton<?php echo $ConvertGuiCounter1; ?>" name="shareXfilebutton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('sharefileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('sharefilebutton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('shareXfilebutton<?php echo $ConvertGuiCounter1; ?>');"/>
          
          <?php } ?>

          <?php if ($AllowUserVirusScan) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>
          <img id="scanfilebutton<?php echo $ConvertGuiCounter1; ?>" name="scanfilebutton<?php echo $ConvertGuiCounter1; ?>" src="Resources/scan.png" style="float:left; display:block;" 
           onclick="toggle_visibility('scanfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('scanfilebutton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('scanfileXbutton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="scanfileXbutton<?php echo $ConvertGuiCounter1; ?>" name="scanfileXbutton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('scanfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('scanfilebutton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('scanfileXbutton<?php echo $ConvertGuiCounter1; ?>');"/>
          
          <?php } ?>
          
          <a style="float:left;">&nbsp;|&nbsp;</a>
          <img id="archfileButton<?php echo $ConvertGuiCounter1; ?>" name="archfileButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/archive.png" style="float:left; display:block;" 
           onclick="toggle_visibility('archfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archfileButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archfileXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="archfileXButton<?php echo $ConvertGuiCounter1; ?>" name="archfileXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('archfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archfileButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archfileXButton<?php echo $ConvertGuiCounter1; ?>');"/>

          <?php if (in_array($extension, $PDFWorkArr)) { ?>          
          <a style="float:left;">&nbsp;|&nbsp;</a>
          
          <img id="docscanButton<?php echo $ConvertGuiCounter1; ?>" name="docscanButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/docscan.png" style="float:left; display:block;" 
           onclick="toggle_visibility('pdfOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('docscanButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('docscanXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="docscanXButton<?php echo $ConvertGuiCounter1; ?>" name="docscanXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('pdfOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('docscanButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('docscanXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $ArchiveArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="archiveButton<?php echo $ConvertGuiCounter1; ?>" name="archiveButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/convert.png" style="float:left; display:block;" 
           onclick="toggle_visibility('archiveOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archiveButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archiveXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="archiveXButton<?php echo $ConvertGuiCounter1; ?>" name="archiveXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('archiveOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archiveButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('archiveXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $DocumentArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="documentButton<?php echo $ConvertGuiCounter1; ?>" name="documentButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/document.png" style="float:left; display:block;" 
           onclick="toggle_visibility('docOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('documentButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('documentXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="documentXButton<?php echo $ConvertGuiCounter1; ?>" name="documentXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('docOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('documentButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('documentXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $SpreadsheetArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="spreadsheetButton<?php echo $ConvertGuiCounter1; ?>" name="spreadsheetButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/spreadsheet.png" style="float:left; display:block;" 
           onclick="toggle_visibility('spreadOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('spreadsheetButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('spreadsheetXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="spreadsheetXButton<?php echo $ConvertGuiCounter1; ?>" name="spreadsheetXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('spreadOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('spreadsheetButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('spreadsheetXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php }

          if (in_array($extension, $PresentationArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="presentationButton<?php echo $ConvertGuiCounter1; ?>" name="presentationButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/presentation.png" style="float:left; display:block;" 
           onclick="toggle_visibility('presentationOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('presentationButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('presentationXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="presentationXButton<?php echo $ConvertGuiCounter1; ?>" name="presentationXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('presentationOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('presentationButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('presentationXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php }

          if (in_array($extension, $ImageArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="imageButton<?php echo $ConvertGuiCounter1; ?>" name="imageButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/photo.png" style="float:left; display:block;" 
           onclick="toggle_visibility('imageOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('imageButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('imageXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="imageXButton<?php echo $ConvertGuiCounter1; ?>" name="imageXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('imageOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('imageButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('imageXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php }

          if (in_array($extension, $MediaArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="mediaButton<?php echo $ConvertGuiCounter1; ?>" name="mediaButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/stream.png" style="float:left; display:block;" 
           onclick="toggle_visibility('audioOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('mediaButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('mediaXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="mediaXButton<?php echo $ConvertGuiCounter1; ?>" name="mediaXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('audioOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('mediaButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('mediaXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $VideoArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="videoButton<?php echo $ConvertGuiCounter1; ?>" name="videoButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/stream.png" style="float:left; display:block;" 
           onclick="toggle_visibility('videoOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('videoButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('videoXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="videoXButton<?php echo $ConvertGuiCounter1; ?>" name="videoXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('videoOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('videoButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('videoXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $DrawingArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="drawingButton<?php echo $ConvertGuiCounter1; ?>" name="drawingButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/convert.png" style="float:left; display:block;" 
           onclick="toggle_visibility('drawingOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('drawingButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('drawingXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="drawingXButton<?php echo $ConvertGuiCounter1; ?>" name="drawingXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('drawingOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('drawingButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('drawingXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } 

          if (in_array($extension, $ModelArray)) { ?>
          <a style="float:left;">&nbsp;|&nbsp;</a>

          <img id="modelButton<?php echo $ConvertGuiCounter1; ?>" name="modelButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/convert.png" style="float:left; display:block;" 
           onclick="toggle_visibility('modelOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('modelButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('modelXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <img id="modelXButton<?php echo $ConvertGuiCounter1; ?>" name="modelXButton<?php echo $ConvertGuiCounter1; ?>" src="Resources/x.png" style="float:left; display:none;" 
           onclick="toggle_visibility('modelOptionsDiv<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('modelButton<?php echo $ConvertGuiCounter1; ?>'); toggle_visibility('modelXButton<?php echo $ConvertGuiCounter1; ?>');"/>
          <?php } ?>
        </div>

        <div id='archfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='archfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>存档此文件</strong></p>
          <p>指定文件名： <input type="text" id='userarchfilefilename<?php echo $ConvertGuiCounter1; ?>' name='userarchfilefilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='archfileextension<?php echo $ConvertGuiCounter1; ?>' name='archfileextension<?php echo $ConvertGuiCounter1; ?>'> 
            <option value="zip">文件格式</option>
            <option value="zip">Zip</option>
            <option value="rar">Rar</option>
            <option value="tar">Tar</option>
            <option value="7z">7z</option>
          </select></p>
          <input type="submit" id="archfileSubmit<?php echo $ConvertGuiCounter1; ?>" name="archfileSubmit<?php echo $ConvertGuiCounter1; ?>" value='存档文件' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#archfileSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  archive:'<?php echo $File; ?>',
                  filesToArchive:'<?php echo $File; ?>',
                  archextension:document.getElementById('archfileextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userfilename:document.getElementById('userarchfilefilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userarchfilefilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('archfileextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userarchfilefilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('archfileextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                    alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>

        <?php if ($AllowUserShare) { ?>
        <div id='sharefileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='sharefileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>分享这个文件</strong></p>
          <p id='sharelinkStatus<?php echo $ConvertGuiCounter1; ?>' name='sharelinkStatus<?php echo $ConvertGuiCounter1; ?>'>链接状态： <i>未生成</i></p>
          <p id='shareclipStatus<?php echo $ConvertGuiCounter1; ?>' name='shareclipStatus<?php echo $ConvertGuiCounter1; ?>'>剪贴板状态： <i>未复制</i></p>
          <p id='sharelinkURL<?php echo $ConvertGuiCounter1; ?>' name='sharelinkURL<?php echo $ConvertGuiCounter1; ?>'>文件链接： <i>未生成</i></p>

          <input type="submit" id="sharegeneratebutton<?php echo $ConvertGuiCounter1; ?>" name="sharegeneratebutton<?php echo $ConvertGuiCounter1; ?>" value='生成链接并复制到剪贴板' onclick="toggle_visibility('loadingCommandDiv');">
          <input type="submit" id="sharecopybutton<?php echo $ConvertGuiCounter1; ?>" name="sharecopybutton<?php echo $ConvertGuiCounter1; ?>" value='生成链接' onclick="toggle_visibility('loadingCommandDiv');">

          <script type="text/javascript">
          $(document).ready(function () {
            $('#sharegeneratebutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
              type: 'POST',
              url: 'convertCore.php',
              data: { 
                Token1:'<?php echo $Token1; ?>',
                Token2:'<?php echo $Token2; ?>',
                download:'<?php echo $File; ?>' },
              success: function(returnFile) {
                toggle_visibility('loadingCommandDiv');
                document.getElementById('sharelinkStatus<?php echo $ConvertGuiCounter1; ?>').innerHTML = '链接状态： <i>生成</i>';
                document.getElementById('shareclipStatus<?php echo $ConvertGuiCounter1; ?>').innerHTML = '剪贴板状态： <i>已复制</i>';
                document.getElementById('sharelinkURL<?php echo $ConvertGuiCounter1; ?>').innerHTML = '文件链接： <i><?php echo $FullURL.'/DATA/'.$SesHash3.'/'.$File; ?></i>';
                copy_share_link("<?php echo $FullURL.'/DATA/'.$SesHash3.'/'.$File; ?>");
                alert("<?php echo $Alert2; ?>"); },
              error: function(ReturnData) {
                alert("<?php echo $Alert3; ?>"); } }); });
            $('#sharecopybutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
              type: 'POST',
              url: 'convertCore.php',
              data: { 
                Token1:'<?php echo $Token1; ?>',
                Token2:'<?php echo $Token2; ?>',
                download:'<?php echo $File; ?>' },
              success: function(returnFile) {
                toggle_visibility('loadingCommandDiv');
                document.getElementById('sharelinkStatus<?php echo $ConvertGuiCounter1; ?>').innerHTML = '链接状态： <i>生成</i>';
                document.getElementById('sharelinkURL<?php echo $ConvertGuiCounter1; ?>').innerHTML = '文件链接： <i><?php echo $FullURL.'/DATA/'.$SesHash3.'/'.$File; ?></i>'; },
              error: function(ReturnData) {
                alert("<?php echo $Alert3; ?>"); } }); }); });
          </script>
        </div>
        <?php }

        if ($AllowUserVirusScan) { ?>
        <div id='scanfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='scanfileOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>扫描此文件以查找病毒</strong></p>
          <input type="submit" id="scancorebutton<?php echo $ConvertGuiCounter1; ?>" name="scancorebutton<?php echo $ConvertGuiCounter1; ?>" value='使用 ScanCore 扫描文件' onclick="toggle_visibility('loadingCommandDiv');">
          <input type="submit" id="clamscanbutton<?php echo $ConvertGuiCounter1; ?>" name="clamscanbutton<?php echo $ConvertGuiCounter1; ?>" value='使用 ClamAV 扫描文件' onclick="toggle_visibility('loadingCommandDiv');">
          <input type="submit" id="scanallbutton<?php echo $ConvertGuiCounter1; ?>" name="scanallbutton<?php echo $ConvertGuiCounter1; ?>" value='使用 ScanCore 和 ClamAV 扫描文件' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#scancorebutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  scantype:'scancore',
                  filesToScan:'<?php echo $File; ?>' },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:'<?php echo $ConsolidatedLogFileName; ?>' },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'.$ConsolidatedLogFileName; ?>"; 
                      document.getElementById('downloadTarget').click(); } }); },
                  error: function(ReturnData) {
                    alert("<?php echo $Alert1; ?>"); } }); });
            $('#clamscanbutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  scantype:'all',
                  filesToScan:'<?php echo $File; ?>' },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:'<?php echo $ConsolidatedLogFileName; ?>' },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'.$ConsolidatedLogFileName; ?>"; 
                      document.getElementById('downloadTarget').click(); } }); },
                  error: function(ReturnData) {
                    alert("<?php echo $Alert1; ?>"); } }); });
            $('#scanallbutton<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  scantype:'all',
                  filesToScan:'<?php echo $File; ?>' },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:'<?php echo $ConsolidatedLogFileName; ?>' },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'.$ConsolidatedLogFileName; ?>"; 
                      document.getElementById('downloadTarget').click(); } }); },
                  error: function(ReturnData) {
                    alert("<?php echo $Alert1; ?>"); } }); }); });
          </script>
        </div>
        <?php }

        if (in_array($extension, $PDFWorkArr)) { 
        ?>
        <div id='pdfOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='pdfOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>对该文件执行光学字符识别</strong></p>
          <p>指定文件名： <input type="text" id='userpdffilename<?php echo $ConvertGuiCounter1; ?>' name='userpdffilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='pdfmethod<?php echo $ConvertGuiCounter1; ?>' name='pdfmethod<?php echo $ConvertGuiCounter1; ?>'>   
            <option value="0">方法</option>  
            <option value="1">方法 1 (简单的)</option>
            <option value="2">方法 2 (先进的)</option>
          </select>
          <select id='pdfextension<?php echo $ConvertGuiCounter1; ?>' name='pdfextension<?php echo $ConvertGuiCounter1; ?>'>   
            <option value="pdf">文件格式</option> 
            <option value="pdf">Pdf</option>   
            <option value="doc">Doc</option>
            <option value="docx">Docx</option>
            <option value="rtf">Rtf</option>
            <option value="txt">Txt</option>
            <option value="odt">Odt</option>
          </select></p>
          <p><input type="submit" id='pdfconvertSubmit<?php echo $ConvertGuiCounter1; ?>' name='pdfconvertSubmit<?php echo $ConvertGuiCounter1; ?>' value='转换成文档' onclick="toggle_visibility('loadingCommandDiv');"></p>
          <script type="text/javascript">
          $(document).ready(function () {
            $('#pdfconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  pdfworkSelected:'<?php echo $File; ?>',
                  method:document.getElementById('pdfmethod<?php echo $ConvertGuiCounter1; ?>').value,
                  pdfextension:document.getElementById('pdfextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userpdfconvertfilename:document.getElementById('userpdffilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userpdffilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('pdfextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userpdffilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('pdfextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $ArchiveArray)) {
        ?>
        <div id='archiveOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='archiveOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此档案</strong></p>
          <p>指定文件名： <input type="text" id='userarchivefilename<?php echo $ConvertGuiCounter1; ?>' name='userarchivefilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='archiveextension<?php echo $ConvertGuiCounter1; ?>' name='archiveextension<?php echo $ConvertGuiCounter1; ?>'> 
            <option value="zip">文件格式</option>
            <option value="zip">Zip</option>
            <option value="rar">Rar</option>
            <option value="tar">Tar</option>
            <option value="7z">7z</option>
          </select></p>
          <input type="submit" id="archiveconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="archiveconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='存档文件' onclick="toggle_visibility('loadingCommandDiv'); display:none;">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#archiveconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('archiveextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userarchivefilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userarchivefilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('archiveextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userarchivefilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('archiveextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $DocumentArray)) {
        ?>
        <div id='docOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='docOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此文档</strong></p>
          <p>指定文件名： <input type="text" id='userdocfilename<?php echo $ConvertGuiCounter1; ?>' name='userdocfilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='docextension<?php echo $ConvertGuiCounter1; ?>' name='docextension<?php echo $ConvertGuiCounter1; ?>'> 
            <option value="txt">文件格式</option>
            <option value="doc">Doc</option>
            <option value="docx">Docx</option>
            <option value="rtf">Rtf</option>
            <option value="txt">Txt</option>
            <option value="odt">Odt</option>
            <option value="pdf">Pdf</option>
          </select></p>
          <input type="submit" id="docconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="docconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换文档' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#docconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('docextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userdocfilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userdocfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('docextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userdocfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('docextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php }

        if (in_array($extension, $SpreadsheetArray)) {
        ?>
        <div id='spreadOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='spreadOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此电子表格</strong></p>
          <p>指定文件名： <input type="text" id='userspreadfilename<?php echo $ConvertGuiCounter1; ?>' name='userspreadfilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='spreadextension<?php echo $ConvertGuiCounter1; ?>' name='spreadextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="ods">文件格式</option> 
            <option value="xls">Xls</option>
            <option value="xlsx">Xlsx</option>
            <option value="ods">Ods</option>
            <option value="pdf">Pdf</option>
          </select></p>
          <input type="submit" id="spreadconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="spreadconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换电子表格' onclick="toggle_visibility('loadingCommandDiv');">        
          <script type="text/javascript">
          $(document).ready(function () {
            $('#spreadconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('spreadextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userspreadfilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userspreadfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('spreadextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userspreadfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('spreadextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); }
                    }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php }

        if (in_array($extension, $PresentationArray)) {
        ?>
        <div id='presentationOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='presentationOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此演示文稿</strong></p>
          <p>指定文件名： <input type="text" id='userpresentationfilename<?php echo $ConvertGuiCounter1; ?>' name='userpresentationfilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='presentationextension<?php echo $ConvertGuiCounter1; ?>' name='presentationextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="odp">文件格式</option>
            <option value="pages">Pages</option>
            <option value="pptx">Pptx</option>
            <option value="ppt">Ppt</option>
            <option value="xps">Xps</option>
            <option value="potx">Potx</option>
            <option value="pot">Pot</option>
            <option value="ppa">Ppa</option>
            <option value="odp">Odp</option>
          </select></p>
          <input type="submit" id="presentationconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="presentationconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换演示文稿' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#presentationconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('presentationextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userpresentationfilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userpresentationfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('presentationextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userspreadfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('presentationextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $MediaArray)) {
        ?>
        <div id='audioOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='audioOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此音频</strong></p>
          <p>指定文件名： <input type="text" id='useraudiofilename<?php echo $ConvertGuiCounter1; ?>' name='useraudiofilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='audioextension<?php echo $ConvertGuiCounter1; ?>' name='audioextension<?php echo $ConvertGuiCounter1; ?>'> 
            <option value="mp3">文件格式</option>
            <option value="mp2">Mp2</option>  
            <option value="mp3">Mp3</option>
            <option value="wav">Wav</option>
            <option value="wma">Wma</option>
            <option value="flac">Flac</option>
            <option value="ogg">Ogg</option>
          </select></p>
          <input type="submit" id="audioconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="audioconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换音频' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#audioconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('audioextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('useraudiofilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('useraudiofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('audioextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('useraudiofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('audioextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $VideoArray)) {
        ?>
        <div id='videoOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='videoOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此视频</strong></p>
          <p>指定文件名： <input type="text" id='uservideofilename<?php echo $ConvertGuiCounter1; ?>' name='uservideofilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='videoextension<?php echo $ConvertGuiCounter1; ?>' name='videoextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="mp4">文件格式</option> 
            <option value="3gp">3gp</option> 
            <option value="mkv">Mkv</option> 
            <option value="avi">Avi</option>
            <option value="mp4">Mp4</option>
            <option value="flv">Flv</option>
            <option value="mpeg">Mpeg</option>
            <option value="wmv">Wmv</option>
            <option value="mov">Mov</option>
          </select></p>
          <input type="submit" id="videoconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="videoconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换视频' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#videoconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('videoextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('uservideofilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('uservideofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('videoextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('uservideofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('videoextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $ModelArray)) {
        ?>
        <div id='modelOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='modelOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此三维模型</strong></p>
          <p>指定文件名： <input type="text" id='usermodelfilename<?php echo $ConvertGuiCounter1; ?>' name='usermodelfilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='modelextension<?php echo $ConvertGuiCounter1; ?>' name='modelextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="3ds">文件格式</option>
            <option value="3ds">3ds</option>
            <option value="collada">Collada</option>
            <option value="obj">Obj</option>
            <option value="off">Off</option>
            <option value="ply">Ply</option>
            <option value="stl">Stl</option>
            <option value="ptx">Ptx</option>
            <option value="dxf">Dxf</option>
            <option value="u3d">U3d</option>
            <option value="vrml">Vrml</option>
          </select></p>
          <input type="submit" id="modelconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="modelconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换模型' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#modelconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('modelextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('usermodelfilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('usermodelfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('modelextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('usermodelfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('modelextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $DrawingArray)) {
        ?>
        <div id='drawingOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='drawingOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此技术图纸或矢量文件</strong></p>
          <p>指定文件名： <input type="text" id='userdrawingfilename<?php echo $ConvertGuiCounter1; ?>' name='userdrawingfilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='drawingextension<?php echo $ConvertGuiCounter1; ?>' name='drawingextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="jpg">文件格式</option>
            <option value="svg">Svg</option>
            <option value="dxf">Dxf</option>
            <option value="vdx">Vdx</option>
            <option value="fig">Fig</option>
            <option value="jpg">Jpg</option>
            <option value="png">Png</option>
            <option value="bmp">Bmp</option>
            <option value="pdf">Pdf</option>
          </select></p>
          <input type="submit" id="drawingconvertSubmit<?php echo $ConvertGuiCounter1; ?>" name="drawingconvertSubmit<?php echo $ConvertGuiCounter1; ?>" value='转换绘图' onclick="toggle_visibility('loadingCommandDiv');">     
          <script type="text/javascript">
          $(document).ready(function () {
            $('#drawingconvertSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  extension:document.getElementById('drawingextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userdrawingfilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('drawingfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('drawingextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userdrawingfilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('drawingextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        </div>
        <?php } 

        if (in_array($extension, $ImageArray)) {
        ?>
        <div id='imageOptionsDiv<?php echo $ConvertGuiCounter1; ?>' name='imageOptionsDiv<?php echo $ConvertGuiCounter1; ?>' style="max-width:750px; display:none;">
          <p style="max-width:1000px;"></p>
          <p><strong>转换此图像</strong></p>
          <p>指定文件名： <input type="text" id='userphotofilename<?php echo $ConvertGuiCounter1; ?>' name='userphotofilename<?php echo $ConvertGuiCounter1; ?>' value='<?php echo str_replace('.', '', $FileNoExt); ?>'>
          <select id='photoextension<?php echo $ConvertGuiCounter1; ?>' name='photoextension<?php echo $ConvertGuiCounter1; ?>'>
            <option value="jpg">文件格式</option>
            <option value="jpg">Jpg</option>
            <option value="bmp">Bmp</option>
            <option value="webp">Webp</option>
            <option value="png">Png</option>
            <option value="cin">Cin</option>
            <option value="dds">Dds</option>
            <option value="dib">Dib</option>
            <option value="flif">Flif</option>
            <option value="avif">Avif</option>
          </select></p>
          <p>宽度和高度： </p>
          <p><input type="number" size="4" value="0" id='width<?php echo $ConvertGuiCounter1; ?>' name='width<?php echo $ConvertGuiCounter1; ?>' min="0" max="10000"> X <input type="number" size="4" value="0" id="height<?php echo $ConvertGuiCounter1; ?>" name="height<?php echo $ConvertGuiCounter1; ?>" min="0"  max="10000"></p> 
          <p>旋转： <input type="number" size="3" id='rotate<?php echo $ConvertGuiCounter1; ?>' name='rotate<?php echo $ConvertGuiCounter1; ?>' value="0" min="0" max="359"></p>
          <input type="submit" id='convertPhotoSubmit<?php echo $ConvertGuiCounter1; ?>' name='convertPhotoSubmit<?php echo $ConvertGuiCounter1; ?>' value='转换图像' onclick="toggle_visibility('loadingCommandDiv');">
          <script type="text/javascript">
          $(document).ready(function () {
            $('#convertPhotoSubmit<?php echo $ConvertGuiCounter1; ?>').click(function() {
              $.ajax({
                type: "POST",
                url: 'convertCore.php',
                data: {
                  Token1:'<?php echo $Token1; ?>',
                  Token2:'<?php echo $Token2; ?>',
                  convertSelected:'<?php echo $File; ?>',
                  rotate:$('#rotate<?php echo $ConvertGuiCounter1; ?>').val(),
                  width:$('#width<?php echo $ConvertGuiCounter1; ?>').val(),
                  height:$('#height<?php echo $ConvertGuiCounter1; ?>').val(),
                  extension:document.getElementById('photoextension<?php echo $ConvertGuiCounter1; ?>').value,
                  userconvertfilename:document.getElementById('userphotofilename<?php echo $ConvertGuiCounter1; ?>').value },
                  success: function(ReturnData) {
                    $.ajax({
                    type: 'POST',
                    url: 'convertCore.php',
                    data: { 
                      Token1:'<?php echo $Token1; ?>',
                      Token2:'<?php echo $Token2; ?>',
                      download:document.getElementById('userphotofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('photoextension<?php echo $ConvertGuiCounter1; ?>').value },
                    success: function(returnFile) {
                      toggle_visibility('loadingCommandDiv');
                      document.getElementById('downloadTarget').href = "<?php echo 'DATA/'.$SesHash3.'/'; ?>"+document.getElementById('userphotofilename<?php echo $ConvertGuiCounter1; ?>').value+'.'+document.getElementById('photoextension<?php echo $ConvertGuiCounter1; ?>').value; 
                      document.getElementById('downloadTarget').click(); } }); },
                    error: function(ReturnData) {
                      alert("<?php echo $Alert; ?>"); } }); }); });
          </script>
        <?php } ?>
      </div>
      <hr />
      <?php } ?>
    </div>