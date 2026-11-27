-- Synthetische Demodaten fuer eine lokale Testinstallation des Tippspiels.
-- Alle Personen und Kontaktdaten sind frei erfunden. Vorher database/schema.sql einspielen.
--
-- Logins (Passwort jeweils: demo1234):
--   admin   / admin@example.test   (Betreiber der Tipprunde, Zugriff auf admin.php)
--   tipper1 / tipper1@example.test
--   tipper2 / tipper2@example.test
--
-- Hinweis: Die Anwendung speichert Passwoerter als MD5-Hash (Altbestand).
-- usr_locked: 0 = aktiv, 1 = gesperrt, 2 = noch nicht aktiviert (Aktivierungslink per Mail)

INSERT INTO `usr_user` (`usr_index`, `usr_name`, `usr_vornachname`, `usr_email`, `usr_password`, `usr_hash`, `usr_locked`, `usr_loginfail`, `usr_tir_index__default`, `usr_bezahlt`, `usr_auto`) VALUES
(1, 'admin',   'Alex Admin',    'admin@example.test',   MD5('demo1234'), MD5('admin-demo-hash'),   0, 0, 1, 1, 1),
(2, 'tipper1', 'Toni Tipper',   'tipper1@example.test', MD5('demo1234'), MD5('tipper1-demo-hash'), 0, 0, 1, 1, 1),
(3, 'tipper2', 'Tina Tipperin', 'tipper2@example.test', MD5('demo1234'), MD5('tipper2-demo-hash'), 0, 0, 1, 0, 1);

-- Tipprunde (tir_usr_index = Betreiber; tir_bezahlung = Hinweistext zur Einsatzzahlung)
INSERT INTO `tir_tipprunde` (`tir_index`, `tir_name`, `tir_einsatz`, `tir_usr_index`, `tir_winnercount`, `tir_scndtbl`, `tir_scndtbl_winnercount`, `tir_astrtipp`, `tir_gewinntext`, `tir_bezahlung`, `tir_active`) VALUES
(1, 'Demo-Tipprunde', '10', 1, 3, 1, 1, 1,
 '<b>Gewinnverteilung (Beispiel)</b><ul><li>1. Platz: 50 %</li><li>2. Platz: 30 %</li><li>3. Platz: 20 %</li></ul>',
 'Einsatz bitte an den Betreiber der Tipprunde (Beispieltext).', 1);

INSERT INTO `urm_usrtirmapping` (`urm_index`, `urm_usr_index`, `urm_tir_index`) VALUES
(1, 1, 1), (2, 2, 1), (3, 3, 1);

-- Beispiel-Tipps fuer die ersten beiden Spiele des Spielplans
INSERT INTO `tip_tipp` (`tip_index`, `tip_mat_index`, `tip_usr_index`, `tip_result_home`, `tip_result_away`, `tip_timestamp`, `tip_points_erg`, `tip_points_ten`, `tip_points_aus`, `tip_ki`, `tip_evaluated`) VALUES
(1, 1, 2, 2, 1, NOW(), 0, 0, 0, 0, 0),
(2, 2, 2, 0, 0, NOW(), 0, 0, 0, 0, 0),
(3, 1, 3, 1, 1, NOW(), 0, 0, 0, 0, 0);

-- Beispiel-Chatbeitrag
INSERT INTO `com_comment` (`com_index`, `com_usr_index`, `com_timestamp`, `com_text`, `com_color`, `com_media`, `com_com_index`) VALUES
(1, 1, NOW(), 'Willkommen in der Demo-Tipprunde!', 2, '', 0);

-- Zusaetzliche Einstellungen, die der Code liest, aber nicht im Export enthalten waren
INSERT INTO `set_setting` (`set_key`, `set_value`) VALUES
('cron_secret', 'CHANGE_ME'),
('api_competition_id', '362'),
('openai_api_key', '');
