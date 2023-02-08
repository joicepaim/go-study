package repositorios

import (
	"api/src/model"
	"database/sql"
)

// Representa repositorio de publicacoes
type Publicacoes struct {
	db *sql.DB
}

func NovoRepositorioDePublicacoes(db *sql.DB) *Publicacoes {
	return &Publicacoes{db}

}

func (repositorio Publicacoes) Criar(publicacao model.Publicacao) (uint64, error) {
	statement, erro := repositorio.db.Prepare("insert into publicacoes (titulo, conteudo, autor_id) values (?,?,?)")
	if erro != nil {
		return 0, erro
	}
	defer statement.Close()

	resultado, erro := statement.Exec(publicacao.Titulo, publicacao.Conteudo, publicacao.AutorID)
	if erro != nil {
		return 0, erro
	}

	IDInserido, erro := resultado.LastInsertId()
	if erro != nil {
		return 0, erro
	}

	return uint64(IDInserido), nil
}

func (repositorio Publicacoes) BuscarPorID(ID uint64) (model.Publicacao, error) {
	linhas, erro := repositorio.db.Query(`
		select p.*, u.nick
	    from publicacoes p inner join usuarios u
		on u.id = p.autor_id where p.id = ?`, ID)

	if erro != nil {
		return model.Publicacao{}, erro
	}

	defer linhas.Close()

	var publicacao model.Publicacao

	if linhas.Next() {
		if erro = linhas.Scan(
			&publicacao.ID,
			&publicacao.Titulo,
			&publicacao.Conteudo,
			&publicacao.AutorID,
			&publicacao.Curtidas,
			&publicacao.Data_criacao,
			&publicacao.AutorNick,
		); erro != nil {
			return model.Publicacao{}, erro
		}

	}

	return publicacao, nil

}

func (repositorio Publicacoes) BuscarFeed(ID uint64) ([]model.Publicacao, error) {
	return nil, nil

}
