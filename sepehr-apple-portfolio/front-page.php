<?php
/**
 * Sepehr Apple Portfolio Front Page / Main Resume Landing
 */

get_header();

// Fetch customizable fields
$name_fa = get_theme_mod( 'sepehr_name_fa', 'سپهر طالبی' );
$name_en = get_theme_mod( 'sepehr_name_en', 'Sepehr Talebi' );
$name_de = get_theme_mod( 'sepehr_name_de', 'Sepehr Talebi' );

$title_fa = get_theme_mod( 'sepehr_title_fa', 'توسعه‌دهنده خلاق و طراح رابط کاربری' );
$title_en = get_theme_mod( 'sepehr_title_en', 'Creative Developer & UI Designer' );
$title_de = get_theme_mod( 'sepehr_title_de', 'Kreativer Entwickler & UI-Designer' );

$bio_fa = get_theme_mod( 'sepehr_bio_fa', 'سپهر طالبی هستم، ۲۳ ساله. با اشتیاق فراوان به خلق تجربیات دیجیتال مدرن، سریع و کاربرپسند با استفاده از آخرین فناوری‌های وب.' );
$bio_en = get_theme_mod( 'sepehr_bio_en', 'I am Sepehr Talebi, 23 years old. Highly passionate about crafting modern, ultra-fast, and user-friendly digital experiences using the latest web technologies.' );
$bio_de = get_theme_mod( 'sepehr_bio_de', 'Ich bin Sepehr Talebi, 23 Jahre alt. Mit großer Leidenschaft entwickle ich moderne, ultraschnelle und benutzerfreundliche digitale Erlebnisse mit den neuesten Webtechnologien.' );

$age = get_theme_mod( 'sepehr_age', '23' );
$profile_pic = get_theme_mod( 'sepehr_profile_pic', '' );

// Fallback profile pic using a premium tech-themed developer illustration or placeholder
if ( empty( $profile_pic ) ) {
	$profile_pic = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600&h=600';
}

// Skills List
$skills_list = get_theme_mod( 'sepehr_skills_list', 'React, Vue, WordPress, PHP, JavaScript, Tailwind CSS, UI/UX Design, Git, Node.js, Elementor' );
$skills_array = array_map( 'trim', explode( ',', $skills_list ) );

// Achievements
$achievements_fa = get_theme_mod( 'sepehr_achievements_text_fa', "🏆 رتبه اول در هکاتون ملی طراحی وب ۱۳۹۹\n🚀 پیاده‌سازی بیش از ۴۰ پروژه فعال و بین‌المللی\n🎖️ متخصص برتر وردپرس و توسعه اختصاصی تم" );
$achievements_en = get_theme_mod( 'sepehr_achievements_text_en', "🏆 1st Place in National Web Hackathon 2020\n🚀 Over 40 active international projects developed\n🎖️ Premium WordPress Specialist & Custom Theme Developer" );
$achievements_de = get_theme_mod( 'sepehr_achievements_text_de', "🏆 1. Platz beim Nationalen Web-Hackathon 2020\n🚀 Über 40 aktive internationale Projekte entwickelt\n🎖️ Premium-WordPress-Spezialist & Entwickler von benutzerdefinierten Themes" );

// Education
$education_fa = get_theme_mod( 'sepehr_education_text_fa', "🎓 کارشناسی مهندسی کامپیوتر - دانشگاه سراسری (۱۳۹۸ - ۱۴۰۲)\n🏫 دیپلم ریاضی و فیزیک - دبیرستان نمونه دولتی" );
$education_en = get_theme_mod( 'sepehr_education_text_en', "🎓 B.Sc. in Computer Engineering - State University (2019 - 2023)\n🏫 Mathematics & Physics Diploma - Elite High School" );
$education_de = get_theme_mod( 'sepehr_education_text_de', "🎓 B.Sc. in Computer Engineering - Staatliche Universität (2019 - 2023)\n🏫 Diplom in Mathematik & Physik - Elite-Gymnasium" );

// Experience
$experience_fa = get_theme_mod( 'sepehr_experience_text_fa', "💼 توسعه‌دهنده ارشد فرانت‌اند - شرکت فناوران نوین (۱۴۰۱ - اکنون)\n🛠️ پیاده‌سازی و طراحی رابط کاربری سیستم‌های بانکی مدرن\n\n💼 برنامه‌نویس وردپرس و وب‌مستر - آژانس دیجیتال اپل وب (۱۳۹۹ - ۱۴۰۱)\n🛠️ طراحی و توسعه بیش از ۳۰ تم و پلاگین اختصاصی بر بستر وردپرس" );
$experience_en = get_theme_mod( 'sepehr_experience_text_en', "💼 Senior Frontend Developer - Novin Tech (2022 - Present)\n🛠️ Architecting & styling modern banking user interfaces\n\n💼 WordPress Developer - AppleWeb Digital Agency (2020 - 2022)\n🛠️ Developed 30+ custom premium themes and plugins" );
$experience_de = get_theme_mod( 'sepehr_experience_text_de', "💼 Senior Frontend-Entwickler - Novin Tech (2022 - Heute)\n🛠️ Architektur & Styling moderner Banking-Benutzeroberflächen\n\n💼 WordPress-Entwickler - Digitalagentur AppleWeb (2020 - 2022)\n🛠️ Entwicklung von über 30 maßgeschneiderten Premium-Themes und Plugins" );

// Courses & Certificates
$courses_fa = get_theme_mod( 'sepehr_courses_text_fa', "🏅 مدرک بین‌المللی توسعه دهنده وردپرس - فرانت‌اند مستر\n📜 گواهینامه دوره فوق پیشرفته جاوااسکریپت و ری‌اکت\n🎯 گواهینامه جامع طراحی رابط کاربری UI/UX" );
$courses_en = get_theme_mod( 'sepehr_courses_text_en', "🏅 Certified WordPress Specialist - Frontend Masters\n📜 Advanced JavaScript & React Architect Certification\n🎯 Professional UI/UX & Interaction Design Course Certificate" );
$courses_de = get_theme_mod( 'sepehr_courses_text_de', "🏅 Zertifizierter WordPress-Spezialist - Frontend Masters\n📜 Fortgeschrittenes JavaScript & React Architekt Zertifikat\n🎯 Professionelles UI/UX & Interaktionsdesign Kurszertifikat" );

// Projects
$project1_image = get_theme_mod( 'sepehr_project1_image', '' );
$project2_image = get_theme_mod( 'sepehr_project2_image', '' );

if ( empty( $project1_image ) ) {
	$project1_image = 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800';
}
if ( empty( $project2_image ) ) {
	$project2_image = 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800';
}

$p1_title_fa = get_theme_mod( 'sepehr_project1_title_fa', 'اپلیکیشن موبایل فین‌تک سیب' );
$p1_title_en = get_theme_mod( 'sepehr_project1_title_en', 'Sib Fintech Mobile Application' );
$p1_title_de = get_theme_mod( 'sepehr_project1_title_de', 'Sib Fintech Mobile App' );

$p1_desc_fa = get_theme_mod( 'sepehr_project1_desc_fa', 'یک اپلیکیشن پرداختی شیشه‌ای و فوق‌مدرن با الهام از آی‌او‌اس با امکانات کیف پول چند ارزی.' );
$p1_desc_en = get_theme_mod( 'sepehr_project1_desc_en', 'A gorgeous, iOS-inspired glassmorphism finance app featuring multi-currency wallet management.' );
$p1_desc_de = get_theme_mod( 'sepehr_project1_desc_de', 'Eine wunderschöne, von iOS inspirierte Finanz-App mit Glasmorphismus und Multi-Währungs-Wallet-Verwaltung.' );

$p2_title_fa = get_theme_mod( 'sepehr_project2_title_fa', 'سامانه هوشمند پورتفولیو ساز هوش مصنوعی' );
$p2_title_en = get_theme_mod( 'sepehr_project2_title_en', 'AI Smart Portfolio Builder' );
$p2_title_de = get_theme_mod( 'sepehr_project2_title_de', 'KI Smart Portfolio Builder' );

$p2_desc_fa = get_theme_mod( 'sepehr_project2_desc_fa', 'یک پلتفرم وب تماماً واکنش‌گرا و مدرن که به کاربران اجازه می‌دهد در چند ثانیه سایت پورتفولیوی شخصی بسازند.' );
$p2_desc_en = get_theme_mod( 'sepehr_project2_desc_en', 'A fully responsive and modern web platform allowing users to craft stunning portfolios in seconds.' );
$p2_desc_de = get_theme_mod( 'sepehr_project2_desc_de', 'Eine voll funktionsfähige und moderne Webplattform, mit der Benutzer in Sekundenschnelle atemberaubende Portfolios erstellen können.' );
?>

<div class="main-portfolio-wrapper">
	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-content">
			<div class="profile-image-container">
				<img src="<?php echo esc_url( $profile_pic ); ?>" alt="Sepehr Talebi Profile Picture" class="profile-pic">
				<div class="profile-badge">
					<span class="lang-text fa-text"><?php echo esc_html( $age ); ?> ساله</span>
					<span class="lang-text en-text"><?php echo esc_html( $age ); ?> y/o</span>
					<span class="lang-text de-text"><?php echo esc_html( $age ); ?> J. alt</span>
				</div>
			</div>

			<h1 class="hero-title">
				<span class="lang-text fa-text"><?php echo esc_html( $name_fa ); ?></span>
				<span class="lang-text en-text"><?php echo esc_html( $name_en ); ?></span>
				<span class="lang-text de-text"><?php echo esc_html( $name_de ); ?></span>
			</h1>

			<h2 class="hero-subtitle">
				<span class="lang-text fa-text"><?php echo esc_html( $title_fa ); ?></span>
				<span class="lang-text en-text"><?php echo esc_html( $title_en ); ?></span>
				<span class="lang-text de-text"><?php echo esc_html( $title_de ); ?></span>
			</h2>

			<p class="hero-bio">
				<span class="lang-text fa-text"><?php echo nl2br( esc_html( $bio_fa ) ); ?></span>
				<span class="lang-text en-text"><?php echo nl2br( esc_html( $bio_en ) ); ?></span>
				<span class="lang-text de-text"><?php echo nl2br( esc_html( $bio_de ) ); ?></span>
			</p>

			<div class="hero-buttons">
				<a href="#contact" class="btn btn-primary">
					<span class="lang-text fa-text">ارتباط با من</span>
					<span class="lang-text en-text">Get in Touch</span>
					<span class="lang-text de-text">Kontakt aufnehmen</span>
				</a>
				<a href="#projects" class="btn btn-secondary">
					<span class="lang-text fa-text">مشاهده پروژه‌ها</span>
					<span class="lang-text en-text">View Projects</span>
					<span class="lang-text de-text">Projekte ansehen</span>
				</a>
			</div>
		</div>
	</section>

	<!-- Bento Bento Resume Grid (Modern Apple Layout) -->
	<section class="bento-resume-section">
		<div class="section-title">
			<h3 class="lang-text fa-text">خلاصه رزومه حرفه‌ای</h3>
			<h3 class="lang-text en-text">Professional Resume Summary</h3>
			<h3 class="lang-text de-text">Beruflicher Lebenslauf</h3>
		</div>

		<div class="bento-grid">
			<!-- Skills Card -->
			<div class="bento-card bento-wide bento-skills">
				<div class="bento-card-header">
					<i class="fas fa-brain card-icon"></i>
					<h4 class="lang-text fa-text">مهارت‌های فنی من</h4>
					<h4 class="lang-text en-text">My Tech Stack</h4>
					<h4 class="lang-text de-text">Mein Tech-Stack</h4>
				</div>
				<div class="skills-tag-container">
					<?php foreach ( $skills_array as $skill ) : if ( ! empty( $skill ) ) : ?>
						<span class="skill-tag"><?php echo esc_html( $skill ); ?></span>
					<?php endif; endforeach; ?>
				</div>
			</div>

			<!-- Achievements Card -->
			<div class="bento-card bento-achievements">
				<div class="bento-card-header">
					<i class="fas fa-award card-icon"></i>
					<h4 class="lang-text fa-text">دستاوردهای من</h4>
					<h4 class="lang-text en-text">Achievements</h4>
					<h4 class="lang-text de-text">Erfolge</h4>
				</div>
				<ul class="resume-list">
					<?php
					$items_fa = explode( "\n", $achievements_fa );
					$items_en = explode( "\n", $achievements_en );
					$items_de = explode( "\n", $achievements_de );
					?>
					<?php foreach ( $items_fa as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text fa-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $items_en as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text en-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $items_de as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text de-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
				</ul>
			</div>

			<!-- Education Card -->
			<div class="bento-card bento-education">
				<div class="bento-card-header">
					<i class="fas fa-graduation-cap card-icon"></i>
					<h4 class="lang-text fa-text">تحصیلات</h4>
					<h4 class="lang-text en-text">Education</h4>
					<h4 class="lang-text de-text">Ausbildung</h4>
				</div>
				<ul class="resume-list">
					<?php
					$edu_fa = explode( "\n", $education_fa );
					$edu_en = explode( "\n", $education_en );
					$edu_de = explode( "\n", $education_de );
					?>
					<?php foreach ( $edu_fa as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text fa-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $edu_en as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text en-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $edu_de as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text de-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
				</ul>
			</div>

			<!-- Work Experience Card -->
			<div class="bento-card bento-tall bento-experience">
				<div class="bento-card-header">
					<i class="fas fa-briefcase card-icon"></i>
					<h4 class="lang-text fa-text">تجربه کاری و شغلی</h4>
					<h4 class="lang-text en-text">Work Experience</h4>
					<h4 class="lang-text de-text">Berufserfahrung</h4>
				</div>
				<div class="experience-content">
					<div class="lang-text fa-text"><?php echo nl2br( esc_html( $experience_fa ) ); ?></div>
					<div class="lang-text en-text"><?php echo nl2br( esc_html( $experience_en ) ); ?></div>
					<div class="lang-text de-text"><?php echo nl2br( esc_html( $experience_de ) ); ?></div>
				</div>
			</div>

			<!-- Courses and Certifications Card -->
			<div class="bento-card bento-courses">
				<div class="bento-card-header">
					<i class="fas fa-certificate card-icon"></i>
					<h4 class="lang-text fa-text">دوره‌ها و گواهینامه‌ها</h4>
					<h4 class="lang-text en-text">Courses & Certifications</h4>
					<h4 class="lang-text de-text">Kurse & Zertifikate</h4>
				</div>
				<ul class="resume-list">
					<?php
					$cour_fa = explode( "\n", $courses_fa );
					$cour_en = explode( "\n", $courses_en );
					$cour_de = explode( "\n", $courses_de );
					?>
					<?php foreach ( $cour_fa as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text fa-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $cour_en as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text en-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
					<?php foreach ( $cour_de as $item ) : if ( ! empty( trim( $item ) ) ) : ?>
						<li class="lang-text de-text"><?php echo esc_html( $item ); ?></li>
					<?php endif; endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<!-- Projects Showcase Section -->
	<section id="projects" class="projects-section">
		<div class="section-title">
			<h3 class="lang-text fa-text">پروژه‌های اخیر</h3>
			<h3 class="lang-text en-text">Recent Work</h3>
			<h3 class="lang-text de-text">Aktuelle Arbeiten</h3>
		</div>

		<div class="projects-grid">
			<!-- Project Card 1 -->
			<div class="project-card">
				<div class="project-img-wrapper">
					<img src="<?php echo esc_url( $project1_image ); ?>" alt="Project 1 Shot">
				</div>
				<div class="project-info">
					<h4 class="lang-text fa-text"><?php echo esc_html( $p1_title_fa ); ?></h4>
					<h4 class="lang-text en-text"><?php echo esc_html( $p1_title_en ); ?></h4>
					<h4 class="lang-text de-text"><?php echo esc_html( $p1_title_de ); ?></h4>

					<p class="lang-text fa-text"><?php echo esc_html( $p1_desc_fa ); ?></p>
					<p class="lang-text en-text"><?php echo esc_html( $p1_desc_en ); ?></p>
					<p class="lang-text de-text"><?php echo esc_html( $p1_desc_de ); ?></p>
				</div>
			</div>

			<!-- Project Card 2 -->
			<div class="project-card">
				<div class="project-img-wrapper">
					<img src="<?php echo esc_url( $project2_image ); ?>" alt="Project 2 Shot">
				</div>
				<div class="project-info">
					<h4 class="lang-text fa-text"><?php echo esc_html( $p2_title_fa ); ?></h4>
					<h4 class="lang-text en-text"><?php echo esc_html( $p2_title_en ); ?></h4>
					<h4 class="lang-text de-text"><?php echo esc_html( $p2_title_de ); ?></h4>

					<p class="lang-text fa-text"><?php echo esc_html( $p2_desc_fa ); ?></p>
					<p class="lang-text en-text"><?php echo esc_html( $p2_desc_en ); ?></p>
					<p class="lang-text de-text"><?php echo esc_html( $p2_desc_de ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- Quick Contact Section -->
	<section id="contact" class="contact-section">
		<div class="contact-card bento-card">
			<div class="bento-card-header">
				<i class="fas fa-paper-plane card-icon"></i>
				<h3 class="lang-text fa-text">شروع یک همکاری فوق‌العاده</h3>
				<h3 class="lang-text en-text">Start a Great Project Together</h3>
				<h3 class="lang-text de-text">Starten wir ein gemeinsames Projekt</h3>
			</div>
			<p class="lang-text fa-text">برای مشاوره، همکاری یا سپردن پروژه‌ها با من تماس بگیرید.</p>
			<p class="lang-text en-text">Feel free to reach out to collaborate on a premium website or talk about software engineering.</p>
			<p class="lang-text de-text">Zögern Sie nicht, mich für eine Zusammenarbeit an einer Premium-Website oder für Software-Engineering zu kontaktieren.</p>
			<a href="mailto:<?php echo esc_attr( get_theme_mod( 'sepehr_email', 'sepehr.talebi@example.com' ) ); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
				<i class="fas fa-envelope"></i>
				<span class="lang-text fa-text">ارسال ایمیل</span>
				<span class="lang-text en-text">Send Email</span>
				<span class="lang-text de-text">E-Mail senden</span>
			</a>
		</div>
	</section>
</div>

<style>
	/* Full layout structure styling */
	.main-portfolio-wrapper {
		max-width: 1024px;
		margin: 0 auto;
		padding: 80px 22px 20px 22px;
	}

	/* Hero Section Styling */
	.hero-section {
		text-align: center;
		padding: 80px 0 60px 0;
	}

	.profile-image-container {
		position: relative;
		width: 150px;
		height: 150px;
		margin: 0 auto 24px auto;
	}

	.profile-pic {
		width: 100%;
		height: 100%;
		border-radius: 50%;
		object-fit: cover;
		border: 4px solid;
		box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
	}

	body.dark-mode .profile-pic {
		border-color: rgba(255, 255, 255, 0.15);
	}

	body.light-mode .profile-pic {
		border-color: rgba(0, 0, 0, 0.05);
	}

	.profile-badge {
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%) translateY(50%);
		background: var(--accent-gradient);
		color: #ffffff;
		font-size: 11px;
		font-weight: 600;
		padding: 4px 12px;
		border-radius: 980px;
		box-shadow: 0 4px 10px rgba(0, 113, 227, 0.4);
		white-space: nowrap;
	}

	.hero-title {
		font-size: 48px;
		font-weight: 800;
		letter-spacing: -0.015em;
		margin: 12px 0 8px 0;
	}

	.hero-subtitle {
		font-size: 24px;
		font-weight: 600;
		color: var(--accent-color);
		margin: 0 0 24px 0;
		letter-spacing: -0.01em;
	}

	.hero-bio {
		font-size: 17px;
		line-height: 1.6;
		max-width: 600px;
		margin: 0 auto 32px auto;
		letter-spacing: -0.01em;
	}

	body.dark-mode .hero-bio {
		color: var(--text-secondary-dark);
	}

	body.light-mode .hero-bio {
		color: var(--text-secondary-light);
	}

	.hero-buttons {
		display: flex;
		justify-content: center;
		gap: 16px;
	}

	.btn {
		display: inline-block;
		font-size: 14px;
		font-weight: 500;
		padding: 12px 24px;
		border-radius: 980px;
		text-decoration: none;
		transition: all 0.3s;
		cursor: pointer;
	}

	.btn-primary {
		background: var(--accent-gradient);
		color: #ffffff;
		box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
	}

	.btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(0, 113, 227, 0.4);
	}

	.btn-secondary {
		border: 1px solid;
	}

	body.dark-mode .btn-secondary {
		border-color: var(--border-dark);
		color: var(--text-dark);
		background-color: rgba(255, 255, 255, 0.05);
	}

	body.light-mode .btn-secondary {
		border-color: var(--border-light);
		color: var(--text-light);
		background-color: rgba(0, 0, 0, 0.03);
	}

	.btn-secondary:hover {
		transform: translateY(-2px);
	}

	/* Bento Section & Grid Layout */
	.bento-resume-section {
		padding: 60px 0;
	}

	.section-title h3 {
		font-size: 28px;
		font-weight: 700;
		margin-bottom: 32px;
		letter-spacing: -0.01em;
		text-align: center;
	}

	.bento-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 20px;
	}

	/* Bento Cards Styling - Beautiful Glassmorphism Apple Style */
	.bento-card {
		border-radius: 24px;
		padding: 28px;
		backdrop-filter: blur(30px);
		-webkit-backdrop-filter: blur(30px);
		border: 1px solid;
		box-shadow: var(--shadow-dark);
		transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
		position: relative;
		overflow: hidden;
	}

	body.dark-mode .bento-card {
		background-color: var(--glass-bg-dark);
		border-color: var(--border-dark);
		box-shadow: var(--shadow-dark);
	}

	body.light-mode .bento-card {
		background-color: var(--glass-bg-light);
		border-color: var(--border-light);
		box-shadow: var(--shadow-light);
	}

	.bento-card:hover {
		transform: scale(1.02);
	}

	.bento-wide {
		grid-column: span 2;
	}

	.bento-tall {
		grid-row: span 2;
	}

	.bento-card-header {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 20px;
	}

	.bento-card-header h4, .bento-card-header h3 {
		font-size: 18px;
		font-weight: 600;
		margin: 0;
	}

	.card-icon {
		font-size: 20px;
		color: var(--accent-color);
	}

	/* Skills Tag */
	.skills-tag-container {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}

	.skill-tag {
		font-size: 13px;
		font-weight: 500;
		padding: 6px 14px;
		border-radius: 980px;
		border: 1px solid;
		transition: all 0.2s;
	}

	body.dark-mode .skill-tag {
		border-color: var(--border-dark);
		background-color: rgba(255, 255, 255, 0.03);
	}

	body.light-mode .skill-tag {
		border-color: var(--border-light);
		background-color: rgba(0, 0, 0, 0.02);
	}

	.skill-tag:hover {
		border-color: var(--accent-color);
		color: var(--accent-color);
		transform: scale(1.05);
	}

	/* Lists in Bento Resume Cards */
	.resume-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.resume-list li {
		position: relative;
		padding-right: 20px;
		margin-bottom: 14px;
		font-size: 14px;
		line-height: 1.6;
	}

	html[dir="ltr"] .resume-list li {
		padding-right: 0;
		padding-left: 20px;
	}

	.resume-list li::before {
		content: "•";
		position: absolute;
		right: 0;
		color: var(--accent-color);
		font-size: 18px;
	}

	html[dir="ltr"] .resume-list li::before {
		right: auto;
		left: 0;
	}

	.experience-content {
		font-size: 14px;
		line-height: 1.8;
	}

	/* Projects Section Grid */
	.projects-section {
		padding: 60px 0;
	}

	.projects-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 24px;
	}

	.project-card {
		border-radius: 24px;
		overflow: hidden;
		border: 1px solid;
		backdrop-filter: blur(30px);
		-webkit-backdrop-filter: blur(30px);
		transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
	}

	body.dark-mode .project-card {
		background-color: var(--glass-bg-dark);
		border-color: var(--border-dark);
		box-shadow: var(--shadow-dark);
	}

	body.light-mode .project-card {
		background-color: var(--glass-bg-light);
		border-color: var(--border-light);
		box-shadow: var(--shadow-light);
	}

	.project-card:hover {
		transform: translateY(-6px);
	}

	.project-img-wrapper {
		height: 220px;
		overflow: hidden;
		position: relative;
	}

	.project-img-wrapper img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 0.5s;
	}

	.project-card:hover .project-img-wrapper img {
		transform: scale(1.05);
	}

	.project-info {
		padding: 24px;
	}

	.project-info h4 {
		font-size: 18px;
		font-weight: 600;
		margin: 0 0 10px 0;
	}

	.project-info p {
		font-size: 13px;
		line-height: 1.6;
		margin: 0;
	}

	body.dark-mode .project-info p {
		color: var(--text-secondary-dark);
	}

	body.light-mode .project-info p {
		color: var(--text-secondary-light);
	}

	/* Contact Section Container */
	.contact-section {
		padding: 60px 0;
	}

	.contact-card {
		text-align: center;
		max-width: 600px;
		margin: 0 auto;
	}

	.contact-card p {
		font-size: 15px;
		margin-bottom: 24px;
	}

	/* Responsive Styling For Mobile and Tablet Devices */
	@media (max-width: 768px) {
		.hero-title {
			font-size: 34px;
		}

		.hero-subtitle {
			font-size: 18px;
		}

		.bento-grid {
			grid-template-columns: 1fr;
		}

		.bento-wide, .bento-tall {
			grid-column: span 1;
			grid-row: span 1;
		}

		.projects-grid {
			grid-template-columns: 1fr;
		}
	}
</style>

<?php get_footer(); ?>
