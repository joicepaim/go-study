package enderecos

import (
	"fmt"
	"strings"
)

func TipoEndereco(endereco string) string {
	tiposValidos := []string{"Rua", "Avenida", "Rodovia"}
	enderecoMinuscula := strings.ToLower(endereco)
	primeiraPalavraEndereco := strings.Split(enderecoMinuscula, " ")[0]

	EnderecoTemTipoValido := false

	for _, tipo := range tiposValidos {
		if tipo == primeiraPalavraEndereco {
			fmt.Printf("%s   %s/n", tipo, primeiraPalavraEndereco)
			EnderecoTemTipoValido = true
		}
	}

	if EnderecoTemTipoValido == true {
		return primeiraPalavraEndereco

	} else {
		return "Tipo invalido"
	}

}

func main() {
	test := TipoEndereco("Avenida Batista")
	print(test)
}
