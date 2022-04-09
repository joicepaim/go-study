package enderecos

import "testing"

func TestTipoEndereco(t *testing.T) {
	enderecooParaTeste := "Avenida Ruy Barbosa"
	tipoEnderecoEsperado := "Avenida"
	tipoEnderecorecebido := TipoEndereco(enderecooParaTeste)
	if tipoEnderecorecebido != tipoEnderecoEsperado {
		t.Errorf("O tipo recebido %s é diferente do tipo esperado %s", tipoEnderecorecebido, tipoEnderecoEsperado)
	}

}
