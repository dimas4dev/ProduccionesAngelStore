<?php
/**
 * Footer común para todas las páginas
 */
?>

    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <h3>🏪 Producciones Angel</h3>
            <p>Tu tienda de confianza desde 2024</p>
            <p>Ofreciendo productos de calidad con el mejor servicio</p>
            
            <div class="footer-links">
                <a href="index.php">Inicio</a>
                <a href="#productos">Productos</a>
                <a href="index.php?controller=contacto&action=contacto">Contacto</a>
                <a href="#acerca">Acerca de</a>
                <a href="#terminos">Términos y Condiciones</a>
            </div>
            
            <p style="margin-top: 20px; opacity: 0.6;">
                © 2024 Producciones Angel. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <style>
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer h3 {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .footer p {
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .footer-links {
            margin-top: 20px;
        }

        .footer-links a {
            color: #3498db;
            text-decoration: none;
            margin: 0 15px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #2980b9;
        }
    </style>

    <script>
        // Auto-refresh para mantener la sesión activa (solo para usuarios logueados)
        <?php if (isset($usuario) && $usuario): ?>
        setInterval(function() {
            fetch('index.php')
                .then(response => {
                    if (!response.ok) {
                        window.location.href = 'index.php?controller=auth&action=login';
                    }
                })
                .catch(error => {
                    console.log('Error de conexión');
                });
        }, 300000); // Cada 5 minutos
        <?php endif; ?>

        // Smooth scrolling para los enlaces del menú
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
