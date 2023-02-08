package model

import (
	"api/src/seguranca"
	"errors"
	"strings"
	"time"

	"github.com/badoux/checkmail"
)

type Usuario struct {
	ID           uint64    `json:"id,omitempty"`
	Nome         string    `json:"nome,omitempty"`
	Nick         string    `json:"nick,omitempty"`
	Email        string    `json:"email,omitempty,"`
	Senha        string    `json:"senha,omitempty"`
	Data_criacao time.Time `jsom:data_criacao,omitempty`
}

//Preparar vai chamar os metodos validar e formatar
func (usuario *Usuario) Preparar(etapa string) error {
	if erro := usuario.validar(etapa); erro != nil {
		return erro
	}

	if erro := usuario.formatar(etapa); erro != nil {
		return erro
	}
	return nil

}

func (usuario *Usuario) validar(etapa string) error {
	if usuario.Nome == "" {
		return errors.New("O Campo nome é obrigatório")
	}
	if usuario.Nick == "" {
		return errors.New("O Campo Nick é obrigatório")
	}

	if usuario.Email == "" {
		return errors.New("O Campo Email é obrigatório")
	}

	if erro := checkmail.ValidateFormat(usuario.Email); erro != nil {
		return errors.New("Formato de email invalido")
	}
	if etapa == "cadastro" && usuario.Senha == "" {
		return errors.New("O Campo Senha é obrigatório")
	}
	return nil
}

//TrimSpace -> tira os espaços das extremidades
func (usuario *Usuario) formatar(etapa string) error {
	usuario.Nome = strings.TrimSpace(usuario.Nome)
	usuario.Nick = strings.TrimSpace(usuario.Nick)
	usuario.Email = strings.TrimSpace(usuario.Email)
	if etapa == "cadastro" {
		senhaComHash, erro := seguranca.Hash(usuario.Senha)
		if erro != nil {
			return erro
		}
		usuario.Senha = string(senhaComHash)
	}
	return nil
}
