package model

import (
	"errors"
	"strings"
	"time"
)

type Publicacao struct {
	ID           uint64    `json:"id,omitempty"`
	Titulo       string    `json:"titulo,omitempty"`
	Conteudo     string    `json:"conteudo,omitempty"`
	AutorID      uint64    `json:"autorId,omitempty"`
	AutorNick    string    `json:"autorNick,omitempty"`
	Curtidas     uint64    `json:"curtidas"`
	Data_criacao time.Time `json:"data_criacao,omitempty"`
}

func (publicacao *Publicacao) Preparar() error {
	if erro := publicacao.Validar(); erro != nil {
		return erro
	}

	publicacao.formatar()
	return nil

}

func (publicacao *Publicacao) Validar() error {
	if publicacao.Titulo == "" {
		return errors.New("O Título é obrigatório e não pode estar em branco")
	}

	if publicacao.Conteudo == "" {
		return errors.New("O Conteúdo é obrigatório e não pode estar em branco")
	}

	return nil

}

func (publicacao *Publicacao) formatar() {
	publicacao.Titulo = strings.TrimSpace(publicacao.Titulo)
	publicacao.Conteudo = strings.TrimSpace(publicacao.Conteudo)
}
