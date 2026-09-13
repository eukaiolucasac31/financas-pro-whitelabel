/**
 * ============================================================================
 * GESTÃO - MAIN JAVASCRIPT
 * Interações, Animações e Máscaras do Sistema
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // ------------------------------------------------------------------------
    // 1. ANIMAÇÃO DE ENTRADA SUAVE (FADE IN)
    // ------------------------------------------------------------------------
    // Faz a página surgir suavemente ao carregar, dando aspecto de app moderno.
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.4s ease-out';
    requestAnimationFrame(() => {
        document.body.style.opacity = '1';
    });

    // ------------------------------------------------------------------------
    // 2. AUTO-DISMISS PARA ALERTAS (MENSAGENS DE SUCESSO/ERRO)
    // ------------------------------------------------------------------------
    // Alertas de "Salvo com sucesso" somem sozinhos após 4 segundos.
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            
            // Remove do DOM após a animação
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // ------------------------------------------------------------------------
    // 3. MÁSCARA DINÂMICA DE MOEDA (BRL - R$)
    // ------------------------------------------------------------------------
    // Formata o input "Valor" automaticamente enquanto o usuário digita.
    const currencyInputs = document.querySelectorAll('.mask-currency');
    currencyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Remove tudo que não for número
            value = value.replace(/\D/g, "");
            
            // Coloca a vírgula (centavos) e os pontos (milhares)
            value = (value / 100).toFixed(2) + "";
            value = value.replace(".", ",");
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            
            e.target.value = value;
        });
    });

    // ------------------------------------------------------------------------
    // 4. HIGHLIGHT DO MENU ATIVO
    // ------------------------------------------------------------------------
    // Deixa o link do menu superior aceso (cor azul) na página em que o usuário está.
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a:not(.logout)');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.style.backgroundColor = 'var(--bg-hover)';
            link.style.color = 'var(--accent-color)';
            link.style.fontWeight = 'bold';
        }
    });

    // ------------------------------------------------------------------------
    // 5. PROTEÇÃO CONTRA DUPLO CLIQUE EM FORMULÁRIOS
    // ------------------------------------------------------------------------
    // Evita que o usuário clique 2x no botão de salvar e cadastre a mesma coisa 2 vezes.
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                // Se já estiver enviando, não faça nada
                if (form.classList.contains('is-submitting')) {
                    e.preventDefault();
                    return;
                }
                form.classList.add('is-submitting');
                btn.innerHTML = 'Processando...';
                btn.style.opacity = '0.7';
                btn.style.cursor = 'not-allowed';
            }
        });
    });

});