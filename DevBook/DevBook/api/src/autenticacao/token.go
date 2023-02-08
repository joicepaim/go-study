package autenticacao

import (
	"api/src/config"
	"errors"
	"fmt"
	"net/http"
	"strconv"
	"strings"
	"time"

	jwt "github.com/dgrijalva/jwt-go"
)

// func init() {
// 	chave := make([]byte, 64)

// 	if _, erro := rand.Read(chave); erro != nil { //serve para gerar valores aleatórios
// 		log.Fatal(erro)
// 	}

// 	stringBase64 := base64.StdEncoding.EncodeToString(chave)
// 	fmt.Println("chave    - ", stringBase64)
// }

//retorna um token assinado com as permissoes do usuario
func CriarToken(usuarioID uint64) (string, error) {
	permissoes := jwt.MapClaims{}
	permissoes["authorized"] = true
	permissoes["exp"] = time.Now().Add(time.Hour * 6).Unix()
	permissoes["usuarioId"] = usuarioID
	token := jwt.NewWithClaims(jwt.SigningMethodHS512, permissoes)
	return token.SignedString([]byte(config.SecretKey))
}

//Validar token verifica se o token passado na requisição é valido
func ValidarToken(r *http.Request) error {
	tokenString := extrairToken(r)

	//pega a string do token e converte para acessarmos os dados das pemissões
	token, erro := jwt.Parse(tokenString, retornaChaveVerificacao)
	if erro != nil {
		return erro
	}
	if _, ok := token.Claims.(jwt.MapClaims); ok && token.Valid {
		return nil
	}
	return errors.New("Token inválido")

}

func ExtrairUsuarioID(r *http.Request) (uint64, error) {
	tokenString := extrairToken(r)

	//pega a string do token e converte para acessarmos os dados das pemissões
	token, erro := jwt.Parse(tokenString, retornaChaveVerificacao)
	if erro != nil {
		return 0, erro
	}

	if permissoes, ok := token.Claims.(jwt.MapClaims); ok && token.Valid {
		usuarioID, erro := strconv.ParseUint(fmt.Sprintf("%.0f", permissoes["usuarioId"]), 10, 64)
		if erro != nil {
			return 0, erro
		}

		return usuarioID, nil

	}

	return 0, errors.New("Token invalido")

}
func extrairToken(r *http.Request) string {
	token := r.Header.Get("Authorization")

	// Veridica se a string da autorização esta vindo com o Bearer na frente ou não
	if len(strings.Split(token, " ")) == 2 {
		return strings.Split(token, " ")[1]
	}

	return ""

}

// retorna a chave de verificação
func retornaChaveVerificacao(token *jwt.Token) (interface{}, error) {
	// se o metodo que esta sendo usado pertence a uma familia especifica
	if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
		return nil, fmt.Errorf("Método de assinatura inesperado! %v", token.Header["alg"])

	}
	return config.SecretKey, nil

}
