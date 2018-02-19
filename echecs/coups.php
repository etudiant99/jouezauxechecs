<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />  <!-- sert entre autre à avoir les accents -->
    <link rel="stylesheet" href="./css/alors.css" type="text/css" />
    
    <script type="text/javascript" src="jquery-1.3.2.js"></script>
    <script>
        $(document).ready((function(){ $("#lenom").focus();}))
    </script>

	<title>Jouer aux échecs</title>
</head>

<body>

    <?php
    session_start();
    $nopartie = $_SESSION['nopartie'];
    $lescoups = $_SESSION['lescoups'];
    $sortie = null;
    
    echo '<u><b>Liste des coups pour la partie #'.$nopartie.'</u></b><br />';
    echo '('.$_SESSION['lesblancs'].'-'.$_SESSION['lesnoirs'].')<br />';
    echo '<br /><input type="button" value="Impression" onClick="window.print()" /><br /><br />';

    $i = 0;
    foreach($lescoups as $item)
    {
        $i++;
        if ($i%2 == 0)
        {
            $truc = '<span style="width: 70px; text-align: left; ">'.$item.'</span>';
            //echo $item.'<br />';
            echo $truc.'<br />';
        }
        else
        {
            $truc = '<span style="width: 70px; text-align: left; ">'.$item.'</span>';
            $n = parseInt($i/2)+1;
            echo $n.'. '.$truc.'&nbsp;&nbsp;';
        }

    }

                                    
    function parseInt($string)
    {
        if(preg_match('/(\d+)/', $string, $array))
            return $array[1];
        else
            return 0;
    }

    ?>
</body>

</html>