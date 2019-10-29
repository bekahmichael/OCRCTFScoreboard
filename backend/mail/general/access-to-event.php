<h3>Hi,</h3>

<p>
    You requested a access link to event "<?= $event->name ?>". Here it is link: <a href="<?= $login_link ?>"><?= $login_link ?></a>
</p>
<p>
    Your access PIN code: <b><?= strtoupper($event_team->pin) ?></b>
</p>

<p>
    If you did not request the reminder, please ignore the email.
</p>