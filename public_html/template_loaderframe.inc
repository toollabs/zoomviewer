<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <script type="text/javascript">
      var pf, c;
      var stage_text = ['stil working...', 'Obtaining image data from commons...', 'Processing image...', 'Not a valid JPG file!'];

      function done(stage)
      {
        // finished copying now preprocess!
        if (stage == 1)
        {
          run(2);
        }

        // all done, reload to show the flash viewer.
        else if (stage == 2)
        {
          window.location.reload();
        }

        // we got a still busy message 
        else if( stage == 0 )
        {
          c.innerHTML= stage_text[stage];
          setTimeout("window.location.reload()", 10000);
        }

        else
          c.innerHTML = stage_text[stage];
      }

      function run(stage)
      {
        pf = document.getElementById('processingframe');
        pf.src = '<?= $req ?>&stage=' + stage;
        c = document.getElementById('message');
        c.innerHTML= stage_text[stage];
      }  
    </script>

    <style type="text/css">
      html, body { background-color: #000; height: 100%; overflow: hidden; margin: 0; padding: 0; }
      body { font-family: Helvetica, Arial, sans-serif; font-weight: bold; color: #ccc; }
      #processingframe { width: 0; height: 0; display: none; position: absolute; top: 0; right: 0; }
    </style>

  </head>

  <body onload="run(<?= $stage ?>)">
    <iframe id="processingframe"></iframe>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" align="center">
    <tr><td>
    <div align="center">
    <span id="message"></span><br>
    <img src="ajax-loader.gif" border=0>
    </div>
    </td></tr>
    </table>
  </body>
</html>

