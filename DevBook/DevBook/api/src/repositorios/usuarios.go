package repositorios

import (
	"api/src/model"
	"database/sql"
	"fmt"
	"log"
)

type Usuarios struct {
	db *sql.DB
}

//Recebe um banco que será aberto no controller e faz a comunicação com as tabelas
func NovoRepositorioUsuario(db *sql.DB) *Usuarios {
	return &Usuarios{db}
}

// Inserir usuario no banco de dados
func (repositorio Usuarios) Criar(usuario model.Usuario) (uint64, error) {
	statement, err := repositorio.db.Prepare(
		"insert into usuarios (nome,senha,nick,email) values (?,?,?,?)")
	if err != nil {
		return 0, err
	}

	resultado, err := statement.Exec(usuario.Nome, usuario.Senha, usuario.Nick, usuario.Email)

	IDInserido, err := resultado.LastInsertId()
	if err != nil {
		return 0, err
	}

	return uint64(IDInserido), nil

}

func (repositorio Usuarios) BuscarUser(nomeOuNick string) ([]model.Usuario, error) {
	nomeOuNick = fmt.Sprintf("%%%s%%", nomeOuNick) //%nomeouNick%
	log.Println("Nome ou nick 2 ", nomeOuNick, "\n")
	linhas, erro := repositorio.db.Query(
		"select id, nome, nick, email, data_criacao from usuarios where nome LIKE ? or nick LIKE ?",
		nomeOuNick, nomeOuNick)
	if erro != nil {
		return nil, erro
	}

	defer linhas.Close()

	var usuarios []model.Usuario

	// log.Println("linhas ", linhas, "\n")

	for linhas.Next() {
		var usuario model.Usuario

		if erro = linhas.Scan(
			&usuario.ID,
			&usuario.Nome,
			&usuario.Nick,
			&usuario.Email,
			&usuario.Data_criacao,
		); erro != nil {
			return nil, erro
		}

		usuarios = append(usuarios, usuario)

	}

	return usuarios, nil
}

func (repositorio Usuarios) BuscarUserID(id uint64) (model.Usuario, error) {

	log.Println("ID ", id, "\n")

	linha, err := repositorio.db.Query(
		"select id, nome, nick, email, data_criacao from usuarios where id=?",
		id)
	if err != nil {
		return model.Usuario{}, err
	}

	defer linha.Close()

	var usuario model.Usuario

	log.Println("linha ", linha, "\n")

	if linha.Next() {
		if err = linha.Scan(
			&usuario.ID,
			&usuario.Nome,
			&usuario.Nick,
			&usuario.Email,
			&usuario.Data_criacao,
		); err != nil {
			return model.Usuario{}, err
		}
	}

	return usuario, nil
}

func (repositorio Usuarios) ModificarUser(id uint64, usuario model.Usuario) error {

	log.Println("ID ", id, "\n")

	statement, err := repositorio.db.Prepare(
		"update usuarios set nome=?, nick=?, email=? where id=?")
	if err != nil {
		return err
	}

	defer statement.Close()
	if _, err := statement.Exec(usuario.Nome, usuario.Nick, usuario.Email, id); err != nil {
		return err
	}

	return nil
}

func (repositorio Usuarios) DeletarUser(id uint64) error {

	log.Println("ID ", id, "\n")

	statement, err := repositorio.db.Prepare(
		"delete from usuarios where id=?")
	if err != nil {
		return err
	}

	defer statement.Close()
	if _, err := statement.Exec(id); err != nil {
		return err
	}

	return nil
}

//pesquisa user por email e retorna id e senha
func (repositorio Usuarios) BuscarPorEmail(email string) (model.Usuario, error) {

	log.Println("ID ", email, "\n")

	linha, err := repositorio.db.Query(
		"select id, senha from usuarios where email=?",
		email)
	if err != nil {
		return model.Usuario{}, err
	}

	defer linha.Close()

	var usuario model.Usuario

	if linha.Next() {
		if err = linha.Scan(
			&usuario.ID,
			&usuario.Senha,
		); err != nil {
			return model.Usuario{}, err
		}
	}

	return usuario, nil
}

// Seguir usuarios
func (repositorio Usuarios) Seguir(usuarioID, usuarioIdToken uint64) error {
	//insert ignore impede que o usuario siga o mesmo usuario duas vezes sem fazer verificação
	statement, erro := repositorio.db.Prepare("insert ignore into seguidores (usuario_id, seguidor_id) values (?, ?)")
	if erro != nil {
		return erro
	}
	defer statement.Close()

	if _, erro = statement.Exec(usuarioID, usuarioIdToken); erro != nil {
		return erro
	}

	return nil
}

// Deixar de Seguir
func (repositorio Usuarios) PararDeSeguir(usuarioID, usuarioIdToken uint64) error {
	statement, erro := repositorio.db.Prepare("delete from seguidores where usuario_id = ? and seguidor_id = ?")
	if erro != nil {
		return erro
	}
	defer statement.Close()

	if _, erro = statement.Exec(usuarioID, usuarioIdToken); erro != nil {
		return erro
	}

	return nil
}

// Buscar Seguidores
func (repositorio Usuarios) BuscarSeguidor(usuarioID uint64) ([]model.Usuario, error) {
	linhas, erro := repositorio.db.Query(`select u.id, u.nome, u.nick, u.email, u.data_criacao
	from usuarios u inner join seguidores s on u.id = s.seguidor_id where s.usuario_id = ?`, usuarioID)

	if erro != nil {
		return nil, erro
	}

	var usuarios []model.Usuario
	for linhas.Next() {
		var usuario model.Usuario

		if erro = linhas.Scan(
			&usuario.ID,
			&usuario.Nome,
			&usuario.Nick,
			&usuario.Email,
			&usuario.Data_criacao,
		); erro != nil {
			return nil, erro
		}

		usuarios = append(usuarios, usuario)
	}

	return usuarios, nil

}

// Buscar usuarios seguidos
func (repositorio Usuarios) BuscarSeguindo(usuarioID uint64) ([]model.Usuario, error) {
	linhas, erro := repositorio.db.Query(`select u.id, u.nome, u.nick, u.email, u.data_criacao
	from usuarios u inner join seguidores s on u.id = s.usuario_id where s.seguidor_id = ?`, usuarioID)

	if erro != nil {
		return nil, erro
	}

	var usuarios []model.Usuario
	for linhas.Next() {
		var usuario model.Usuario

		if erro = linhas.Scan(
			&usuario.ID,
			&usuario.Nome,
			&usuario.Nick,
			&usuario.Email,
			&usuario.Data_criacao,
		); erro != nil {
			return nil, erro
		}

		usuarios = append(usuarios, usuario)
	}

	return usuarios, nil

}

// BuscarSenha traz a senha de um usario pelo ID
func (repositorio Usuarios) BuscarSenha(usuarioID uint64) (string, error) {
	linha, erro := repositorio.db.Query("select senha from usuarios where id = ?", usuarioID)
	if erro != nil {
		return "", erro
	}
	defer linha.Close()

	var usuario model.Usuario

	if linha.Next() {
		if erro = linha.Scan(&usuario.Senha); erro != nil {
			return "", erro
		}

	}

	return usuario.Senha, nil
}

func (repositorio Usuarios) AtualizarSenha(usuarioID uint64, senha string) error {
	statement, erro := repositorio.db.Prepare("update usuarios set senha = ? where id = ?")
	if erro != nil {
		return erro
	}

	defer statement.Close()

	if _, erro = statement.Exec(senha, usuarioID); erro != nil {
		return erro
	}

	return nil
}
