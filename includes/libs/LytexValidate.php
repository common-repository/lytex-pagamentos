<?php
/**
 * LyTex Validation Class.
 */

class LytexValidate {

	public function _payment_method( $data ): bool {

		if ($data != 'billet' && $data != 'pix'){
			return false;
		}

		return true;

	}

	public function _name( $data ): bool {

		$validation = preg_match( "/^[ ]*(?:[^\\s]+[ ]+)+[^\\s]+[ ]*$/", $data );
		if ( ! $validation || strlen( $data ) < 2 ) {
			return false;
		}

		return true;

	}

	public function _person_type( $data ): bool {

		if( $data != "1" && $data != "2" ){
			return false;
		}

		return true;

	}

	public function _pf_pj( $data ): bool {

		if ($data[0] == '1'){
			return $this->_cpf($data[1]);
		} else {
			return $this->_cnpj($data[1]);
		}

	}

	public function _cpf( $data ): bool {

		if ( empty( $data ) ) {
			return false;
		}

		$cpf = preg_replace( '/[^0-9]/', '', $data );
		$cpf = str_pad( $cpf, 11, '0', STR_PAD_LEFT );

		if ( strlen( $cpf ) != 11 ) {
			return false;
		} elseif ( $cpf == '00000000000' ||
		           $cpf == '11111111111' ||
		           $cpf == '22222222222' ||
		           $cpf == '33333333333' ||
		           $cpf == '44444444444' ||
		           $cpf == '55555555555' ||
		           $cpf == '66666666666' ||
		           $cpf == '77777777777' ||
		           $cpf == '88888888888' ||
		           $cpf == '99999999999'
		) {
			return false;
		} else {
			for ( $t = 9; $t < 11; $t ++ ) {

				for ( $d = 0, $c = 0; $c < $t; $c ++ ) {
					$d += $cpf[$c] * (($t + 1) - $c);
				}
				$d = ( ( 10 * $d ) % 11 ) % 10;
				if ($cpf[$c] != $d) {
					return false;
				}
			}
		}

		return true;

	}

	public function _cnpj( $cnpj ): bool {

		if ( empty( $cnpj ) ) {
			return false;
		}

		$cnpj = preg_replace( '/[^0-9]/', '', (string) $cnpj );

		if ( strlen( $cnpj ) != 14 ) {
			return false;
		}

		for ( $i = 0, $j = 5, $soma = 0; $i < 12; $i ++ ) {
			$soma += $cnpj[$i] * $j;
			$j    = ( $j == 2 ) ? 9 : $j - 1;
		}
		$resto = $soma % 11;
		if ( $cnpj[12] != ( $resto < 2 ? 0 : 11 - $resto ) ) {
			return false;
		}

		for ( $i = 0, $j = 6, $soma = 0; $i < 13; $i ++ ) {
			$soma += $cnpj[$i] * $j;
			$j    = ( $j == 2 ) ? 9 : $j - 1;
		}
		$resto = $soma % 11;

		return $cnpj[13] == ( $resto < 2 ? 0 : 11 - $resto );

	}

	public function _phone( $data ): bool {

		$phone      = preg_replace( '/[^0-9]/', '', $data );
		if(strlen($phone) > 10){
			$validation = preg_match( "/^[1-9]{2}9?[0-9]{9}$/", $phone );
		}else{
			$validation = preg_match( "/^[1-9]{2}9?[0-9]{8}$/", $phone );
		}
		if ( ! $validation ) {
			return false;
		}

		return true;

	}

	public function _email( $data ): bool {

		$validation = preg_match( "/^[A-Za-z0-9_\\-]+(?:[.][A-Za-z0-9_\\-]+)*@[A-Za-z0-9_]+(?:[-.][A-Za-z0-9_]+)*\\.[A-Za-z0-9_]+$/", $data );
		if ( ! $validation ) {
			return false;
		}

		return true;

	}

	public function _zip_code( $data ): bool {

		$zipcode = preg_replace( '/[^0-9]/', '', $data );
		if ( strlen( $zipcode ) < 8 ) {
			return false;
		}

		return true;

	}

	public function _state( $data ): bool {

		$validation = preg_match( "/^(?:A[CLPM]|BA|CE|DF|ES|GO|M[ATSG]|P[RBAEI]|R[JNSOR]|S[CEP]|TO)$/", $data );
		if ( ! $validation ) {
			return false;
		}

		return true;

	}

	public function _street( $data ): bool {

		if ( strlen( $data ) < 2 || strlen( $data ) > 200 ) {
			return false;
		}

		return true;

	}

	public function _number( $data ): bool {

		if ( strlen( $data ) < 1 || strlen( $data ) > 55 ) {
			return false;
		}

		return true;

	}

	public function _city( $data ): bool {

		if ( strlen( $data ) < 2 || strlen( $data ) > 255 ) {
			return false;
		}

		return true;

	}

	public function _zone( $data ): bool {

		if ( strlen( $data ) < 1 || strlen( $data ) > 255 ) {
			return false;
		}

		return true;

	}

}