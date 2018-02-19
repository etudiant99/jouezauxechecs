    <div class="titreveritable">
        Envoyer un message
    </div>
    <div id="envoi_message">
        <form id="formulairemessage" method="post" action="index.php?module=parties&amp;action=traitermessage.php">
            <label for="lenom">Pseudonyme du destinataire: </label><input required="true" id="lenom" type="text" name="destinataire" value="" />
            <label for="lesujet">Sujet: </label><input required="true" type="text" name="sujet" id="lesujet" value="" />
            <br /><br /><label for="lemessage">Texte du message: </label><textarea required="true" cols="50" rows="6" name="contenu" id="lemessage"></textarea>
            <br /><input type="submit" value="Envoyer" />
        </form>
    </div>