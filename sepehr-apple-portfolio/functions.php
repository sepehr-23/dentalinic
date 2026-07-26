<?php
/**
 * Sepehr Apple Portfolio Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function sepehr_apple_theme_setup() {
	// Add support for translation
	load_theme_textdomain( 'sepehr-apple-portfolio', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Register Navigation Menus
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'sepehr-apple-portfolio' ),
	) );

	// Support HTML5 markup
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Support custom logo
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Support Elementor
	add_theme_support( 'elementor-pages' );
}
add_action( 'after_setup_theme', 'sepehr_apple_theme_setup' );

/**
 * Enqueue scripts and styles.
 */
function sepehr_apple_theme_scripts() {
	// Enqueue Vazirmatn Font for Persian
	wp_enqueue_style( 'vazirmatn-font', 'https://fastly.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/vazirmatn-font-face.css', array(), '33.003' );

	// Enqueue SF Pro or Inter-like fonts for premium Apple style
	wp_enqueue_style( 'inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', array(), '1.0' );

	// FontAwesome for gorgeous icons
	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );

	// Main stylesheet
	wp_enqueue_style( 'sepehr-apple-style', get_stylesheet_uri(), array(), '1.0.0' );

	// Custom scripts for dark/light mode, language switching, and interactive elements
	wp_enqueue_script( 'sepehr-apple-scripts', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'sepehr_apple_theme_scripts' );

/**
 * Register Customizer options for editable fields (Resume, Info, Skills, Projects, etc. in 3 languages)
 */
function sepehr_apple_theme_customize_register( $wp_customize ) {
	// --- SECTION: PERSONAL INFORMATION ---
	$wp_customize->add_section( 'sepehr_personal_info', array(
		'title'       => __( 'اطلاعات شخصی (Personal Info)', 'sepehr-apple-portfolio' ),
		'priority'    => 30,
		'description' => __( 'اطلاعات پایه سپهر طالبی (نام، سن، تصویر و...)', 'sepehr-apple-portfolio' ),
	) );

	// Profile Picture
	$wp_customize->add_setting( 'sepehr_profile_pic', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sepehr_profile_pic', array(
		'label'    => __( 'عکس پروفایل سپهر', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_personal_info',
		'settings' => 'sepehr_profile_pic',
	) ) );

	// Name (Farsi, English, German)
	$languages = array(
		'fa' => 'فارسی',
		'en' => 'English',
		'de' => 'Deutsch'
	);

	foreach ( $languages as $lang_code => $lang_name ) {
		// Name
		$wp_customize->add_setting( "sepehr_name_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'سپهر طالبی' : 'Sepehr Talebi',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_name_{$lang_code}", array(
			'label'    => sprintf( __( 'نام (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_personal_info',
			'type'     => 'text',
		) );

		// Subtitle / Bio Title
		$wp_customize->add_setting( "sepehr_title_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'توسعه‌دهنده خلاق و طراح رابط کاربری' : ( ( $lang_code == 'en' ) ? 'Creative Developer & UI Designer' : 'Kreativer Entwickler & UI-Designer' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_title_{$lang_code}", array(
			'label'    => sprintf( __( 'عنوان شغلی / تخصص (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_personal_info',
			'type'     => 'text',
		) );

		// Bio Text / Intro
		$wp_customize->add_setting( "sepehr_bio_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'سپهر طالبی هستم، ۲۳ ساله. با اشتیاق فراوان به خلق تجربیات دیجیتال مدرن، سریع و کاربرپسند با استفاده از آخرین فناوری‌های وب.' : ( ( $lang_code == 'en' ) ? 'I am Sepehr Talebi, 23 years old. Highly passionate about crafting modern, ultra-fast, and user-friendly digital experiences using the latest web technologies.' : 'Ich bin Sepehr Talebi, 23 Jahre alt. Mit großer Leidenschaft entwickle ich moderne, ultraschnelle und benutzerfreundliche digitale Erlebnisse mit den neuesten Webtechnologien.' ),
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( "sepehr_bio_{$lang_code}", array(
			'label'    => sprintf( __( 'درباره من / خلاصه مشخصات (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_personal_info',
			'type'     => 'textarea',
		) );
	}

	// Age and Social links
	$wp_customize->add_setting( 'sepehr_age', array(
		'default'           => '23',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sepehr_age', array(
		'label'    => __( 'سن', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_personal_info',
		'type'     => 'text',
	) );

	// Email
	$wp_customize->add_setting( 'sepehr_email', array(
		'default'           => 'sepehr.talebi@example.com',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'sepehr_email', array(
		'label'    => __( 'ایمیل ارتباطی', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_personal_info',
		'type'     => 'email',
	) );

	// GitHub Link
	$wp_customize->add_setting( 'sepehr_github', array(
		'default'           => 'https://github.com',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sepehr_github', array(
		'label'    => __( 'لینک گیت‌هاب', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_personal_info',
		'type'     => 'url',
	) );

	// LinkedIn Link
	$wp_customize->add_setting( 'sepehr_linkedin', array(
		'default'           => 'https://linkedin.com',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'sepehr_linkedin', array(
		'label'    => __( 'لینک لینکدین', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_personal_info',
		'type'     => 'url',
	) );


	// --- SECTION: SKILLS (مهارت‌ها) ---
	$wp_customize->add_section( 'sepehr_skills_section', array(
		'title'       => __( 'مهارت‌ها (Skills)', 'sepehr-apple-portfolio' ),
		'priority'    => 31,
		'description' => __( 'لیست مهارت‌های خود را با فرمت خط به خط یا کاما جدا شده بنویسید.', 'sepehr-apple-portfolio' ),
	) );

	$wp_customize->add_setting( 'sepehr_skills_list', array(
		'default'           => 'React, Vue, WordPress, PHP, JavaScript, Tailwind CSS, UI/UX Design, Git, Node.js, Elementor',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'sepehr_skills_list', array(
		'label'       => __( 'لیست مهارت‌ها (با ویرگول جدا کنید)', 'sepehr-apple-portfolio' ),
		'description' => __( 'مثال: PHP, JavaScript, React, WordPress', 'sepehr-apple-portfolio' ),
		'section'     => 'sepehr_skills_section',
		'type'        => 'text',
	) );


	// --- SECTION: ACHIEVEMENTS (دستاوردها) ---
	$wp_customize->add_section( 'sepehr_achievements_section', array(
		'title'       => __( 'دستاوردها (Achievements)', 'sepehr-apple-portfolio' ),
		'priority'    => 32,
	) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_achievements_text_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? "🏆 رتبه اول در هکاتون ملی طراحی وب ۱۳۹۹\n🚀 پیاده‌سازی بیش از ۴۰ پروژه فعال و بین‌المللی\n🎖️ متخصص برتر وردپرس و توسعه اختصاصی تم" : ( ( $lang_code == 'en' ) ? "🏆 1st Place in National Web Hackathon 2020\n🚀 Over 40 active international projects developed\n🎖️ Premium WordPress Specialist & Custom Theme Developer" : "🏆 1. Platz beim Nationalen Web-Hackathon 2020\n🚀 Über 40 aktive internationale Projekte entwickelt\n🎖️ Premium-WordPress-Spezialist & Entwickler von benutzerdefinierten Themes" ),
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( "sepehr_achievements_text_{$lang_code}", array(
			'label'    => sprintf( __( 'لیست دستاوردها (%s) - هر مورد در یک خط', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_achievements_section',
			'type'     => 'textarea',
		) );
	}


	// --- SECTION: EDUCATION (تحصیلات) ---
	$wp_customize->add_section( 'sepehr_education_section', array(
		'title'       => __( 'تحصیلات (Education)', 'sepehr-apple-portfolio' ),
		'priority'    => 33,
	) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_education_text_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? "🎓 کارشناسی مهندسی کامپیوتر - دانشگاه سراسری (۱۳۹۸ - ۱۴۰۲)\n🏫 دیپلم ریاضی و فیزیک - دبیرستان نمونه دولتی" : ( ( $lang_code == 'en' ) ? "🎓 B.Sc. in Computer Engineering - State University (2019 - 2023)\n🏫 Mathematics & Physics Diploma - Elite High School" : "🎓 B.Sc. in Computer Engineering - Staatliche Universität (2019 - 2023)\n🏫 Diplom in Mathematik & Physik - Elite-Gymnasium" ),
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( "sepehr_education_text_{$lang_code}", array(
			'label'    => sprintf( __( 'سوابق تحصیلی (%s) - هر مورد در یک خط', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_education_section',
			'type'     => 'textarea',
		) );
	}


	// --- SECTION: EXPERIENCE (سوابق شغلی) ---
	$wp_customize->add_section( 'sepehr_experience_section', array(
		'title'       => __( 'سوابق شغلی (Experience)', 'sepehr-apple-portfolio' ),
		'priority'    => 34,
	) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_experience_text_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? "💼 توسعه‌دهنده ارشد فرانت‌اند - شرکت فناوران نوین (۱۴۰۱ - اکنون)\n🛠️ پیاده‌سازی و طراحی رابط کاربری سیستم‌های بانکی مدرن\n\n💼 برنامه‌نویس وردپرس و وب‌مستر - آژانس دیجیتال اپل وب (۱۳۹۹ - ۱۴۰۱)\n🛠️ طراحی و توسعه بیش از ۳۰ تم و پلاگین اختصاصی بر بستر وردپرس" : ( ( $lang_code == 'en' ) ? "💼 Senior Frontend Developer - Novin Tech (2022 - Present)\n🛠️ Architecting & styling modern banking user interfaces\n\n💼 WordPress Developer - AppleWeb Digital Agency (2020 - 2022)\n🛠️ Developed 30+ custom premium themes and plugins" : "💼 Senior Frontend-Entwickler - Novin Tech (2022 - Heute)\n🛠️ Architektur & Styling moderner Banking-Benutzeroberflächen\n\n💼 WordPress-Entwickler - Digitalagentur AppleWeb (2020 - 2022)\n🛠️ Entwicklung von über 30 maßgeschneiderten Premium-Themes und Plugins" ),
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( "sepehr_experience_text_{$lang_code}", array(
			'label'    => sprintf( __( 'سوابق شغلی (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_experience_section',
			'type'     => 'textarea',
		) );
	}


	// --- SECTION: COURSES & CERTIFICATES (دوره‌ها و گواهینامه‌ها) ---
	$wp_customize->add_section( 'sepehr_courses_section', array(
		'title'       => __( 'دوره‌ها و گواهینامه‌ها (Courses & Certificates)', 'sepehr-apple-portfolio' ),
		'priority'    => 35,
	) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_courses_text_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? "🏅 مدرک بین‌المللی توسعه دهنده وردپرس - فرانت‌اند مستر\n📜 گواهینامه دوره فوق پیشرفته جاوااسکریپت و ری‌اکت\n🎯 گواهینامه جامع طراحی رابط کاربری UI/UX" : ( ( $lang_code == 'en' ) ? "🏅 Certified WordPress Specialist - Frontend Masters\n📜 Advanced JavaScript & React Architect Certification\n🎯 Professional UI/UX & Interaction Design Course Certificate" : "🏅 Zertifizierter WordPress-Spezialist - Frontend Masters\n📜 Fortgeschrittenes JavaScript & React Architekt Zertifikat\n🎯 Professionelles UI/UX & Interaktionsdesign Kurszertifikat" ),
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( "sepehr_courses_text_{$lang_code}", array(
			'label'    => sprintf( __( 'دوره‌ها و گواهینامه‌ها (%s) - هر مورد در یک خط', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_courses_section',
			'type'     => 'textarea',
		) );
	}


	// --- SECTION: PROJECTS (پروژه‌ها) ---
	$wp_customize->add_section( 'sepehr_projects_section', array(
		'title'       => __( 'پروژه‌ها (Projects)', 'sepehr-apple-portfolio' ),
		'priority'    => 36,
		'description' => __( 'تنظیمات بخش پروژه‌های انجام شده (با استایل بی‌نظیر شیشه‌ای اپل)', 'sepehr-apple-portfolio' ),
	) );

	// Project 1
	$wp_customize->add_setting( 'sepehr_project1_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sepehr_project1_image', array(
		'label'    => __( 'عکس پروژه اول', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_projects_section',
		'settings' => 'sepehr_project1_image',
	) ) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_project1_title_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'اپلیکیشن موبایل فین‌تک سیب' : ( ( $lang_code == 'en' ) ? 'Sib Fintech Mobile Application' : 'Sib Fintech Mobile App' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_project1_title_{$lang_code}", array(
			'label'    => sprintf( __( 'عنوان پروژه اول (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_projects_section',
			'type'     => 'text',
		) );

		$wp_customize->add_setting( "sepehr_project1_desc_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'یک اپلیکیشن پرداختی شیشه‌ای و فوق‌مدرن با الهام از آی‌او‌اس با امکانات کیف پول چند ارزی.' : ( ( $lang_code == 'en' ) ? 'A gorgeous, iOS-inspired glassmorphism finance app featuring multi-currency wallet management.' : 'Eine wunderschöne, von iOS inspirierte Finanz-App mit Glasmorphismus und Multi-Währungs-Wallet-Verwaltung.' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_project1_desc_{$lang_code}", array(
			'label'    => sprintf( __( 'توضیح پروژه اول (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_projects_section',
			'type'     => 'text',
		) );
	}

	// Project 2
	$wp_customize->add_setting( 'sepehr_project2_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'sepehr_project2_image', array(
		'label'    => __( 'عکس پروژه دوم', 'sepehr-apple-portfolio' ),
		'section'  => 'sepehr_projects_section',
		'settings' => 'sepehr_project2_image',
	) ) );

	foreach ( $languages as $lang_code => $lang_name ) {
		$wp_customize->add_setting( "sepehr_project2_title_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'سامانه هوشمند پورتفولیو ساز هوش مصنوعی' : ( ( $lang_code == 'en' ) ? 'AI Smart Portfolio Builder' : 'KI Smart Portfolio Builder' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_project2_title_{$lang_code}", array(
			'label'    => sprintf( __( 'عنوان پروژه دوم (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_projects_section',
			'type'     => 'text',
		) );

		$wp_customize->add_setting( "sepehr_project2_desc_{$lang_code}", array(
			'default'           => ( $lang_code == 'fa' ) ? 'یک پلتفرم وب تماماً واکنش‌گرا و مدرن که به کاربران اجازه می‌دهد در چند ثانیه سایت پورتفولیوی شخصی بسازند.' : ( ( $lang_code == 'en' ) ? 'A fully responsive and modern web platform allowing users to craft stunning portfolios in seconds.' : 'Eine voll funktionsfähige und moderne Webplattform, mit der Benutzer in Sekundenschnelle atemberaubende Portfolios erstellen können.' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( "sepehr_project2_desc_{$lang_code}", array(
			'label'    => sprintf( __( 'توضیح پروژه دوم (%s)', 'sepehr-apple-portfolio' ), $lang_name ),
			'section'  => 'sepehr_projects_section',
			'type'     => 'text',
		) );
	}
}
add_action( 'customize_register', 'sepehr_apple_theme_customize_register' );
