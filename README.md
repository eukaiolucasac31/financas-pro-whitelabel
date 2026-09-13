# 1 - Gestão - Sistema Financeiro Whitelabel (SaaS)

Um sistema completo de gestão financeira e controle de fluxo de caixa desenvolvido em **PHP Nativo (PDO)**, estruturado com arquitetura **Whitelabel (Marca Branca)**. O software foi pensado para ser comercializado para pequenas e médias empresas ou utilizado como base para produtos SaaS.

---

## 2 - Principais Funcionalidades

- **2.1 Painel Whitelabel Dinâmico:** O administrador pode alterar o nome da empresa, o Logotipo e o Favicon diretamente pelo painel de controle no navegador, sem precisar mexer em linhas de código.
- **2.2 Interface Dark Theme (Steam Style):** Design moderno, responsivo e focado na experiência do usuário (UX), inspirado em grandes plataformas digitais.
- **🛡️ Segurança Avançada (Enterprise Level):** 
  - Proteção robusta contra ataques de **SQL Injection** com uso estrito de *Prepared Statements* via PDO.
  - Prevenção contra ataques **CSRF** e fixação de sessão (*Session Fixation*).
  - Validação real de arquivos enviados via MIME Type para evitar uploads maliciosos.
  - Hashing seguro de senhas utilizando a API nativa do PHP (`password_hash`).
- **2.3 Inteligência e Gráficos:** Dashboard interativo em tempo real integrado com **Chart.js** para acompanhamento visual da evolução financeira dos últimos meses.
- **2.4 Motor de Contas Recorrentes:** Automação em background que gera lançamentos mensais recorrentes (como assinaturas e contratos) automaticamente.
- **2.5 Exportação Nativa para PDF:** Relatórios DRE simplificados prontos para impressão ou salvamento em PDF otimizado para o padrão A4.

---

## 3 - Tecnologias Utilizadas

- **Backend:** PHP 8.0+ (Orientado a Segurança e Modularização)
- **Banco de Dados:** MySQL / MariaDB (com suporte a restrições de chave estrangeira e integridade referencial)
- **Frontend:** HTML5, CSS3 Avançado (Variáveis CSS / Custom Properties) e JavaScript (Vanilla ES6+)
- **Bibliotecas Externas:** Chart.js (via CDN)
- **Servidor Web:** Compatível com Apache / Nginx (XAMPP, Laragon, Docker ou cPanel)

---

## 4 - Guia de Instalação Local (XAMPP / Laragon)

1. **Clonagem / Download:** Baixe ou clone este repositório para dentro da pasta do seu servidor web (ex: `htdocs/financas`).
2. **Configuração de Ambiente:** 
   - Copie o arquivo `config/config.example.php` e renomeie-o para `config/config.php`.
   - Edite o arquivo `config.php` informando as suas credenciais do MySQL e a URL base correta do seu projeto na constante `BASE_URL`.
3. **Banco de Dados:** 
   - Abra o seu gerenciador MySQL (como o phpMyAdmin).
   - Importe o arquivo contido em `database/schema.sql` para criar a estrutura do banco e os dados iniciais.
4. **Permissões de Pasta:** Certifique-se de que a pasta `uploads/` e suas subpastas (`logos` e `icons`) possuem permissão de escrita.
5. **Acesso Inicial:** 
   - Acesse via navegador: `http://localhost/sua-pasta-do-projeto/`
   - **E-mail de Acesso:** `admin@empresa.com`
   - **Senha Padrão:** `admin123`

---

## 5 - Demonstração Visual (Screenshots)

- **Dashboard & Gráficos:** <img width="1911" height="929" alt="image" src="https://github.com/user-attachments/assets/5179b3bd-9fa4-470d-aba9-b40d696f5523" />
- **Relatórios:** <img width="1911" height="927" alt="image" src="https://github.com/user-attachments/assets/a848dd85-5eeb-4ef0-8762-805bb6a0101c" />


- **Lançamentos & Máscaras:** <img width="1911" height="926" alt="image" src="https://github.com/user-attachments/assets/d4997627-0c3a-4f94-9cd7-d1e9c43a6a0b" />

- **Painel Whitelabel:** <img width="1911" height="931" alt="image" src="https://github.com/user-attachments/assets/ef91a0f1-b9e2-4fe5-8353-72ada09b2656" />


---

## 6 - Licenciamento

Este software é distribuído sob licença comercial proprietária. A revenda, cópia não autorizada ou redistribuição do código-fonte sem a devida licença é estritamente proibida.

---
*Desenvolvido por Kaio Lucas*
