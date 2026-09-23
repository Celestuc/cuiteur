<?php
/**
 * Gestion des exceptions pour les exemples du tutoriel
 *
 * @author François Piat
 *
 */
class MonException extends Exception {
	/** @var bool Indique si phase développement ou production */
	public static bool $is_dev = false;
	/** @var bool Indique si arrêt quand erreur */
	private bool $stop;
	/** @var array Texte des messages d'erreur */
	private array $textes = [
						'Erreur inconnue',
						'Modèle invalide',
						'Nombre de cordes invalide',
						'Prix invalide'];
	
	/**
	 * Constructeur
	 *
	 * @param string|int	$p1		Texte ou numéro d'erreur
	 * @param bool			$p2		Indique si arrêt après erreur
	 */
	public function __construct(string|int $p1, bool $p2 = true) {
		if (is_int($p1)) {
			($p1 < 0 || $p1 >= count($this->textes)) && $p1 = 0;
			$msg = $this->textes[$p1];
		} else {
			$p1 = trim($p1);
			if (trim($p1) != '') {
				$msg = $p1;
			} else {
				$msg = $this->textes[0];
			}
		}
		parent::__construct($msg);
		$this->stop = $p2;
	}
	/**
	 * Affichage du message d'erreur et arrêt éventuel du script
	 *
	 */
	public function __toString() : string {
		if (! self::$is_dev) {
			if ($this->stop) {
				ob_end_clean();
				exit ('<hr>Site inaccessible<hr>');
			}
			return '';
		}

		echo '<hr><b>Exception capturée : </b>',
			$this->getMessage(),
			str_replace('#', '<br>#', $this->getTraceAsString()),
			'<hr>';

		if ($this->stop) {
			exit;
		}
		return '';
	}
}
