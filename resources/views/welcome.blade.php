<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBTA 86 | Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
        .card-premium { transition: 0.4s ease; background: white; }
        .card-premium:hover { transform: scale(1.03); box-shadow: 0 25px 50px -12px rgba(255, 102, 0, 0.25); }
        .nav-link:hover { color: #FF6600; }
        
        /* Animación del carrusel */
        @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-scroll { display: flex; animation: scroll 25s linear infinite; width: fit-content; }
        .animate-scroll:hover { animation-play-state: paused; }

        /* Estilo Tarjeta Noticia */
        .news-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
            border-bottom: 5px solid #FF6600;
            cursor: pointer;
        }
        .news-card:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
            box-shadow: 0 20px 25px -5px rgba(0, 51, 153, 0.15);
            transform: translateY(-10px);
        }
    </style>
</head>
<body class="bg-slate-50">

    <!-- Navegación -->
    <nav class="bg-[#003399] text-white p-6 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="font-black text-2xl tracking-tighter">CBTA 86</h1>
            <div class="hidden md:flex gap-8 font-bold">
                <a href="#" class="nav-link">Inicio</a>
                <a href="#hoy" class="nav-link">CBTA 86 HOY</a>
                <a href="#" class="nav-link">Servicios</a>
                <a href="{{ route('login') }}" class="bg-orange-600 px-5 py-2 rounded-full hover:bg-orange-500 transition">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative h-[80vh] flex items-center justify-center text-white text-center px-6">
        <div class="absolute inset-0 bg-[#003399]"></div>
        <div class="relative z-10 max-w-4xl">
            <h2 class="text-orange-500 font-black text-xl uppercase tracking-widest mb-4">Tu Futuro Comienza Aquí</h2>
            <h1 class="text-6xl md:text-8xl font-black mb-6 uppercase">CBTA 86</h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-10">Excelencia técnica, innovación agropecuaria y el mejor ambiente para tu desarrollo integral.</p>
            <a href="#fichas" class="bg-orange-600 hover:bg-orange-500 text-white px-10 py-4 rounded-full font-bold text-lg transition-transform hover:scale-105 shadow-xl animate-pulse">
                ¡Quiero mi Ficha!
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16">
        <!-- Carreras -->
        <section class="mb-20">
            <h2 class="text-4xl font-black text-[#003399] mb-12 text-center">Carreras Técnicas</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="card-premium p-8 rounded-3xl border-t-8 border-orange-500 shadow-lg">
                    <div class="text-4xl mb-4">💻</div>
                    <h3 class="font-bold text-xl mb-2">Técnico en Ofimática</h3>
                </div>
                <div class="card-premium p-8 rounded-3xl border-t-8 border-orange-500 shadow-lg">
                    <div class="text-4xl mb-4">🚜</div>
                    <h3 class="font-bold text-xl mb-2">Técnico Agropecuario</h3>
                </div>
                <div class="card-premium p-8 rounded-3xl border-t-8 border-orange-500 shadow-lg">
                    <div class="text-4xl mb-4">🍞</div>
                    <h3 class="font-bold text-xl mb-2">Producción Industrial de Alimentos</h3>
                </div>
                <div class="card-premium p-8 rounded-3xl border-t-8 border-orange-500 shadow-lg">
                    <div class="text-4xl mb-4">📈</div>
                    <h3 class="font-bold text-xl mb-2">Administración de Emprendimientos</h3>
                </div>
            </div>
        </section>

        <!-- Nueva Sección: Instalaciones y Servicios -->
        <section class="mb-20">
            <h2 class="text-4xl font-black text-[#003399] mb-12 text-center">Instalaciones y Servicios</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-blue-50">
                    <h3 class="text-xl font-black text-orange-600 mb-4 uppercase">Infraestructura</h3>
                    <ul class="text-slate-600 space-y-2">
                        <li><i class="fas fa-check text-blue-900 mr-2"></i> <strong>29 Aulas didácticas:</strong> Incluyen Sala audiovisual, Sala de medios, Biblioteca y comedor/cafetería.</li>
                        <li><i class="fas fa-check text-blue-900 mr-2"></i> <strong>Laboratorios:</strong> Química y suelos, Física y biología, Usos múltiples y Cómputo.</li>
                        <li><i class="fas fa-check text-blue-900 mr-2"></i> <strong>Talleres didácticos:</strong> Frutas y hortalizas, Cárnicos, Lácteos y Panificación.</li>
                        <li><i class="fas fa-check text-blue-900 mr-2"></i> <strong>Áreas agropecuarias:</strong> Posta bovina, Porcina y Ovina (6 hectáreas para uso agrícola).</li>
                        <li><i class="fas fa-check text-blue-900 mr-2"></i> <strong>Canchas deportivas:</strong> Fútbol, Básquetbol y Voleibol.</li>
                    </ul>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-blue-50">
                    <h3 class="text-xl font-black text-orange-600 mb-4 uppercase">Servicios Estudiantiles</h3>
                    <p class="text-slate-600 leading-relaxed mb-4">Brindamos atención integral para tu desarrollo académico y personal:</p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-slate-700"><i class="fas fa-award text-orange-500"></i> Becas del Gobierno Federal</li>
                        <li class="flex items-center gap-3 text-slate-700"><i class="fas fa-shield-alt text-orange-500"></i> Seguro de vida y médico (IMSS)</li>
                        <li class="flex items-center gap-3 text-slate-700"><i class="fas fa-user-graduate text-orange-500"></i> Orientación educativa y vocacional</li>
                        <li class="flex items-center gap-3 text-slate-700"><i class="fas fa-chalkboard-teacher text-orange-500"></i> Programa de tutorías personalizadas</li>
                        <li class="flex items-center gap-3 text-slate-700"><i class="fas fa-palette text-orange-500"></i> Actividades paraescolares</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Entrega de Fichas -->
        <section id="fichas" class="grid lg:grid-cols-2 gap-12 items-center bg-[#003399] p-12 md:p-20 rounded-[3rem] text-white shadow-2xl mb-20">
            <div>
                <h2 class="text-5xl font-black mb-6">Entrega de Fichas</h2>
                <p class="text-blue-200 text-lg mb-8">El primer paso para ser parte del CBTA 86. No dejes pasar las fechas.</p>
                <div class="bg-orange-600 p-8 rounded-2xl">
                    <p class="text-sm uppercase font-bold tracking-widest">Inversión única</p>
                    <p class="text-5xl font-black">$400.00 MXN</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="border border-white/20 p-6 rounded-2xl">
                    <h4 class="font-bold text-xl mb-2 text-orange-400">📅 Periodo y Horarios</h4>
                    <p>Del 20 de Abril al 30 de Junio</p>
                    <p class="font-bold">09:00 a 11:00 y 12:00 a 14:00</p>
                </div>
                <div class="border border-white/20 p-6 rounded-2xl">
                    <h4 class="font-bold text-xl mb-2 text-orange-400">📋 Requisitos para Ficha</h4>
                    <ul class="space-y-2">
                        <li>• CURP actualizado.</li>
                        <li>• Constancia de estar inscrito en 3ro de secundaria.</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Requisitos de Inscripción -->
        <section class="mb-20">
            <h2 class="text-4xl font-black text-[#003399] mb-12 text-center">Requisitos de Inscripción</h2>
            <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-[2rem] shadow-xl border border-slate-100">
                <ul class="grid md:grid-cols-2 gap-4 text-slate-700">
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Acta de nacimiento (original y 3 copias)</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Certificado de secundaria (original y 3 copias)</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Carta de buena conducta (original y copia)</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Constancia de autenticidad de certificado de secundaria</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> CURP (nuevo formato)</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Certificado Médico (grupo sanguíneo, alergias, peso, estatura, enfermedades crónicas)</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Número de Seguro Social</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Comprobante de domicilio</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Copia del INE del tutor legal</li>
                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-orange-500 mt-1"></i> Correo electrónico y teléfono celular de contacto</li>
                </ul>
            </div>
        </section>

        <!-- Calendario de Actividades -->
        <section class="mb-20 bg-slate-100 p-12 rounded-[3rem] text-center">
            <h2 class="text-4xl font-black text-[#003399] mb-12">Calendario de Actividades</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm">
                    <div class="text-4xl mb-4">🎓</div>
                    <h3 class="font-bold text-xl mb-2">Plática de Inducción</h3>
                    <p class="text-slate-600 font-bold">4 de Julio de 2026 (Sábado)</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm">
                    <div class="text-4xl mb-4">📝</div>
                    <h3 class="font-bold text-xl mb-2">Evaluación Diagnóstica</h3>
                    <p class="text-slate-600 font-bold">13 al 17 de Julio de 2026</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm">
                    <div class="text-4xl mb-4">📚</div>
                    <h3 class="font-bold text-xl mb-2">Curso Propedéutico</h3>
                    <p class="text-slate-600 font-bold">24 de Agosto al 4 de Septiembre 2026</p>
                </div>
            </div>
        </section>

        <!-- CBTA 86 HOY -->
        <section id="hoy" class="mb-20 overflow-hidden">
        <h2 class="text-4xl font-black text-[#003399] mb-8 text-center">CBTA 86 HOY</h2>
        <div class="relative">
            <div class="animate-scroll gap-8">
                <!-- Artículos -->
                <article class="news-card p-8 rounded-3xl border border-slate-100 min-w-[320px] md:min-w-[400px]">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-orange-100 text-orange-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Evento</span>
                        <span class="text-slate-400 text-xs font-bold">26 Mayo 2026</span>
                    </div>
                    <h3 class="font-black text-xl text-slate-800 mb-3 leading-tight">Torneo Interno de Fútbol</h3>
                    <p class="text-slate-600 text-sm">Competencia deportiva entre especialidades. ¡Ven a apoyar a tu grupo favorito!</p>
                </article>

                <article class="news-card p-8 rounded-3xl border border-slate-100 min-w-[320px] md:min-w-[400px]">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-blue-100 text-[#003399] text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Aviso</span>
                        <span class="text-slate-400 text-xs font-bold">25 Mayo 2026</span>
                    </div>
                    <h3 class="font-black text-xl text-slate-800 mb-3 leading-tight">Reunión de Padres</h3>
                    <p class="text-slate-600 text-sm">Seguimiento tutorial y entrega de reportes del segundo parcial.</p>
                </article>

                <article class="news-card p-8 rounded-3xl border border-slate-100 min-w-[320px] md:min-w-[400px]">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-green-100 text-green-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Taller</span>
                        <span class="text-slate-400 text-xs font-bold">24 Mayo 2026</span>
                    </div>
                    <h3 class="font-black text-xl text-slate-800 mb-3 leading-tight">Exposición de Lácteos</h3>
                    <p class="text-slate-600 text-sm">Demostración técnica de los procesos de la especialidad de Alimentos.</p>
                </article>
                
                <!-- Duplicado para efecto infinito -->
                <article class="news-card p-8 rounded-3xl border border-slate-100 min-w-[320px] md:min-w-[400px]">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-orange-100 text-orange-600 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">Evento</span>
                        <span class="text-slate-400 text-xs font-bold">26 Mayo 2026</span>
                    </div>
                    <h3 class="font-black text-xl text-slate-800 mb-3 leading-tight">Torneo Interno de Fútbol</h3>
                    <p class="text-slate-600 text-sm">Competencia deportiva entre especialidades. ¡Ven a apoyar a tu grupo favorito!</p>
                </article>
            </div>
        </div>
    </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white pt-16 pb-8 px-6">
        <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-12 text-center md:text-left mb-12">
            <div>
                <h3 class="text-orange-500 font-bold mb-4 flex justify-center md:justify-start items-center gap-2"><i class="fas fa-map-marker-alt"></i> Ubicación</h3>
                <p>Arcadio Pagaza 4, Colonia Magisterio</p>
                <p>Perote, Veracruz. C.P. 91273</p>
            </div>
            <div>
                <h3 class="text-orange-500 font-bold mb-4 flex justify-center md:justify-start items-center gap-2"><i class="fas fa-phone"></i> Contacto</h3>
                <a href="tel:+522828250352" class="hover:text-orange-400 transition block">282 825 0352</a>
            </div>
            <div>
                <h3 class="text-orange-500 font-bold mb-4 flex justify-center md:justify-start items-center gap-2"><i class="fas fa-share-alt"></i> Redes Sociales</h3>
                <a href="#" class="hover:text-orange-400 transition flex items-center justify-center md:justify-start gap-2"><i class="fab fa-facebook text-2xl"></i> Cbta 86 Perote Veracruz</a>
            </div>
        </div>
        <div class="text-center border-t border-slate-800 pt-8">
            <p class="text-sm text-slate-500">© 2026 CBTA 86. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>