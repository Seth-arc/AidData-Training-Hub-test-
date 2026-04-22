<?php

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

final class AidData_Home_Catalog_Seeder {
	private const OPTION_KEY     = 'aiddata_home_catalog_seed_version';
	private const SEED_VERSION   = '1.2.0';
	private const SEED_META_KEY  = '_aiddata_home_catalog_seed_key';

	/**
	 * Keep seeded terms and starter content aligned after plugin updates.
	 */
	public static function maybe_upgrade(): void {
		$current_version = (string) get_option( self::OPTION_KEY, '' );

		if ( self::SEED_VERSION === $current_version ) {
			return;
		}

		self::seed_defaults();
	}

	/**
	 * Seed the default filter terms and starter cards.
	 */
	public static function seed_defaults(): void {
		self::ensure_terms();

		foreach ( self::get_default_cards() as $card ) {
			self::upsert_default_card( $card );
		}

		update_option( self::OPTION_KEY, self::SEED_VERSION );
	}

	/**
	 * Create the default filter taxonomy.
	 */
	private static function ensure_terms(): void {
		foreach ( self::get_default_terms() as $term ) {
			$existing = get_term_by( 'slug', $term['slug'], AidData_Home_Catalog_Post_Type::TAXONOMY );

			if ( ! $existing instanceof WP_Term ) {
				$result = wp_insert_term(
					$term['name'],
					AidData_Home_Catalog_Post_Type::TAXONOMY,
					array(
						'slug' => $term['slug'],
					)
				);

				if ( is_wp_error( $result ) || empty( $result['term_id'] ) ) {
					continue;
				}

				$term_id = (int) $result['term_id'];
			} else {
				$term_id = (int) $existing->term_id;
			}

			update_term_meta( $term_id, AidData_Home_Catalog_Post_Type::TERM_ORDER_META_KEY, (int) $term['order'] );
		}
	}

	/**
	 * Default categories used by the filter bar.
	 *
	 * @return array<int, array{slug: string, name: string, order: int}>
	 */
	private static function get_default_terms(): array {
		return array(
			array(
				'slug'  => 'course',
				'name'  => 'Courses',
				'order' => 10,
			),
			array(
				'slug'  => 'tutorial',
				'name'  => 'Tutorials',
				'order' => 20,
			),
			array(
				'slug'  => 'interview',
				'name'  => 'Interviews',
				'order' => 30,
			),
			array(
				'slug'  => 'tools',
				'name'  => 'Tools',
				'order' => 40,
			),
			array(
				'slug'  => 'simulation',
				'name'  => 'Simulations',
				'order' => 50,
			),
		);
	}

	/**
	 * Starter cards copied from the existing hard-coded front page.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function get_default_cards(): array {
		$navigating_modal = <<<'HTML'
<div class="course-instructors">
	<h4>Course Instructors</h4>
	<div class="instructor-avatars">
		<div class="instructor">
			<img src="{{theme_url}}assets/images/alex_wooley.jpg" alt="Alex Wooley" class="instructor-avatar">
			<div class="instructor-info">
				<h5>Alex Wooley</h5>
				<p>Director, Partnerships &amp; Communications</p>
			</div>
		</div>
		<div class="instructor">
			<img src="{{theme_url}}assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
			<div class="instructor-info">
				<h5>John Custer</h5>
				<p>Deputy Director, Communications &amp; Data Analytics</p>
			</div>
		</div>
		<div class="instructor">
			<img src="{{theme_url}}assets/images/sethu_nguna.png" alt="Sethu Nguna" class="instructor-avatar">
			<div class="instructor-info">
				<h5>Sethu Nguna</h5>
				<p>Manager, Training &amp; Instructional Design</p>
			</div>
		</div>
	</div>
</div>
<div class="inline-partnership">
	<img src="{{theme_url}}assets/images/wm_logo_white.png" alt="William &amp; Mary Logo">
	<p>Delivered in partnership with the W&amp;M Studio for Teaching and Learning Innovation</p>
</div>
<div class="info-description">
	<h4>About this Course</h4>
	<p>Learn the fundamentals of development finance, from funding sources to implementation strategies. This comprehensive course provides a solid foundation in understanding global development finance mechanisms and practices.</p>
	<h4>Who is this Course for?</h4>
	<ul class="learning-objectives">
		<li>Development finance professionals</li>
		<li>Government officials working in finance and development</li>
		<li>NGO staff involved in project funding</li>
		<li>Students pursuing careers in international development</li>
	</ul>
</div>
HTML;

		$china_modal = <<<'HTML'
<div class="info-description">
	<h4>About this Tutorial</h4>
	<p>Get hands-on experience with AidData's premier tool for tracking Chinese development finance. This quick tutorial will show you how to navigate the dashboard, understand its features, and extract valuable insights about Chinese overseas development projects.</p>
	<h4>What You'll Learn</h4>
	<ul class="learning-objectives">
		<li>How to access and interpret project-level data on Chinese development finance</li>
		<li>Techniques for filtering and searching across 13,000+ projects</li>
		<li>Ways to analyze geographical distribution of Chinese development finance</li>
		<li>Methods for comparing projects across sectors and regions</li>
		<li>Steps to download and cite data for research</li>
	</ul>
</div>
HTML;

		$gie_modal = <<<'HTML'
<div class="course-instructors">
	<h4>Tutorial Instructor</h4>
	<div class="instructor-avatars">
		<div class="instructor">
			<img src="{{theme_url}}assets/images/seth_goodman_workshop.jpg" alt="Dr. Seth Goodman" class="instructor-avatar">
			<div class="instructor-info">
				<h5>Dr. Seth Goodman</h5>
				<p>Research Scientist, Geospatial Impact Evaluations</p>
			</div>
		</div>
	</div>
</div>
<div class="inline-partnership">
	<img src="{{theme_url}}assets/images/wm_logo_white.png" alt="William &amp; Mary Logo">
	<p>Developed by the AidData Research Lab at William &amp; Mary</p>
</div>
<div class="info-description">
	<h4>About this Tutorial</h4>
	<p>Discover AidData's pioneering geospatial impact evaluation methodology that enables rigorous evaluation of development interventions using satellite observations and spatial analysis. Learn how GIEs can measure intended and unintended impacts at a fraction of the time and cost of traditional randomized controlled trials.</p>
	<h4>What You'll Learn</h4>
	<ul class="learning-objectives">
		<li>Understanding the fundamentals of geospatial impact evaluation methodology</li>
		<li>How to leverage satellite data and spatial analysis for impact measurement</li>
		<li>Techniques for establishing reliable counterfactuals using geographic data</li>
		<li>Methods for measuring development outcomes remotely and retrospectively</li>
		<li>Best practices for implementing GIEs in various development contexts</li>
		<li>Real-world case studies demonstrating GIE applications in agriculture, health, and infrastructure</li>
	</ul>
</div>
HTML;

		$credit_tool_modal = <<<'HTML'
<div class="course-instructors">
	<h4>Tool Developers</h4>
	<div class="instructor-avatars">
		<div class="instructor">
			<img src="{{theme_url}}assets/images/john_custer.svg" alt="John Custer" class="instructor-avatar">
			<div class="instructor-info">
				<h5>John Custer</h5>
				<p>Deputy Director, Communications &amp; Data Analytics</p>
			</div>
		</div>
		<div class="instructor">
			<img src="{{theme_url}}assets/images/asad_sami.jpg" alt="Asad Sami" class="instructor-avatar">
			<div class="instructor-info">
				<h5>Asad Sami</h5>
				<p>Senior Program Manager</p>
			</div>
		</div>
	</div>
</div>
<div class="inline-partnership">
	<img src="{{theme_url}}assets/images/wm_logo_white.png" alt="William &amp; Mary Logo">
	<p>Developed by the AidData Research Lab at William &amp; Mary</p>
</div>
<div class="info-description">
	<h4>About this Tool</h4>
	<p>The Credit Evaluation Tool helps teams compare financing offers, model repayment trajectories, and stress-test loan packages against key macroeconomic assumptions. Use it to benchmark terms across lenders and document trade-offs for decision makers.</p>
	<h4>Key Features</h4>
	<ul class="learning-objectives">
		<li>Side-by-side comparison of credit terms across lenders</li>
		<li>Scenario modeling for interest rates, grace periods, and maturities</li>
		<li>Risk flags for collateralization, fees, and covenant structures</li>
		<li>Benchmarking against historical deals and peer countries</li>
		<li>Exportable summaries for approvals and negotiations</li>
	</ul>
</div>
HTML;

		$data_command_modal = <<<'HTML'
<div class="inline-partnership">
	<p>Prototype package: this seeded simulation uses remote preview media so you can validate the homepage card, trailer flow, and modal treatment before replacing it with production simulation assets.</p>
</div>
<div class="info-description">
	<img src="https://images.unsplash.com/photo-1754152365074-b1014729ce37?auto=format&fit=crop&fm=jpg&q=80&w=1600" alt="Operator reviewing multi-screen analytics in a command center" style="width:100%;height:auto;border-radius:16px;">
	<h4>About this Simulation</h4>
	<p><strong>Data Command</strong> is a fast-paced scenario simulation for analysts, journalists, and policy teams coordinating decisions from live financial, geospatial, and media signals. Learners step into a command desk role, monitor changing indicators, and decide which evidence stream deserves immediate attention.</p>
	<h4>Scenario Snapshot</h4>
	<p>A regional infrastructure shock is unfolding across multiple districts. You have thirty minutes to identify exposure hotspots, triage incoming alerts, and publish an evidence-backed recommendation for leadership.</p>
</div>
<div class="info-description">
	<h4>What You'll Do</h4>
	<ul class="learning-objectives">
		<li>Triage conflicting alerts from dashboards, maps, and briefing notes</li>
		<li>Escalate the highest-risk signal before downstream impacts compound</li>
		<li>Balance speed, confidence, and source reliability under time pressure</li>
		<li>Document your reasoning in a final decision brief for reviewers</li>
	</ul>
</div>
<div class="info-description">
	<img src="https://images.unsplash.com/photo-1526628953301-3e589a6a8b74?auto=format&fit=crop&fm=jpg&q=80&w=1600" alt="Close-up dashboard panels showing live quality scores and trend lines" style="width:100%;height:auto;border-radius:16px;">
	<h4>Why it works on the front page</h4>
	<p>This package exercises the exact pieces the homepage plugin needs to prove out a new simulation type: remote card art, a remote trailer asset, a full modal briefing, custom CTA copy, and a dedicated simulation category.</p>
</div>
HTML;

		return array(
			array(
				'seed_key'      => 'navigating-global-development-finance',
				'slug'          => 'navigating-global-development-finance',
				'title'         => 'Navigating Global Development Finance',
				'excerpt'       => 'Learn the fundamentals of development finance, from funding sources to implementation strategies.',
				'menu_order'    => 10,
				'terms'         => array( 'course' ),
				'modal_content' => $navigating_modal,
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/global_finance.png',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'Global Development Finance',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Course\nDigital Badge\nMultimodal",
					AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE           => '12-16 hours',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO           => 'Introductory',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_THREE         => 'Data Journalism',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Start Learning',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => '/t-h/navigating-global-development-finance/',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_LABEL      => 'Watch Trailer',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_URL        => 'https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=ce80f637-c233-465b-a586-b3d801016b15&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=false&interactivity=none',
					AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL         => 'Info',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_PATH   => 'assets/images/data_journalism_badge.png',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_ALT    => 'Course Badge',
				),
			),
			array(
				'seed_key'      => 'global-chinese-development-finance',
				'slug'          => 'global-chinese-development-finance',
				'title'         => 'Global Chinese Development Finance',
				'excerpt'       => 'Learn how to effectively use the China.AidData.org dashboard to explore and analyze Chinese development finance data, track projects, and generate insights.',
				'menu_order'    => 20,
				'terms'         => array( 'tutorial' ),
				'modal_content' => $china_modal,
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/china_dashboard.png',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'China Dashboard Tutorial',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Tutorial\nSovereign Finance\nCertificate",
					AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE           => '30-45 mins',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO           => 'All Levels',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Start Tutorial',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => '/courses/global-chinese-development-finance/',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_LABEL      => 'Watch Preview',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_URL        => 'https://wmedu.hosted.panopto.com/Panopto/Pages/Embed.aspx?id=db8251fc-f910-4fc0-a5d5-b379004d81da&autoplay=false&offerviewer=false&showtitle=false&showbrand=false&captions=true&interactivity=none',
					AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL         => 'Info',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_PATH   => 'assets/images/certificate.png',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_ALT    => 'Tutorial Certificate',
				),
			),
			array(
				'seed_key'      => 'geospatial-impact-evaluations',
				'slug'          => 'geospatial-impact-evaluations',
				'title'         => 'Geospatial Impact Evaluations',
				'excerpt'       => 'An introduction to AidData\'s geospatial impact evaluation methodology for evaluating development interventions using satellite data.',
				'menu_order'    => 30,
				'terms'         => array( 'tutorial' ),
				'modal_content' => $gie_modal,
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/GIE_coursethumbnail.jpg',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'Geospatial Impact Evaluations Tutorial',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Tutorial\nGeospatial Data\nCertificate",
					AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE           => '45-60 mins',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO           => 'Intermediate',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Start Tutorial',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => '/courses/geospatial-impact-evaluations/',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_LABEL      => 'Watch Preview',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_URL        => 'assets/videos/tut1.mp4',
					AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL         => 'Info',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_PATH   => 'assets/images/certificate.png',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_BADGE_ALT    => 'Tutorial Certificate',
				),
			),
			array(
				'seed_key'      => 'data-command',
				'slug'          => 'data-command',
				'title'         => 'Data Command',
				'excerpt'       => 'A scenario-driven simulation where teams monitor live dashboards, triage alerts, and make evidence-backed decisions under pressure.',
				'menu_order'    => 35,
				'terms'         => array( 'simulation' ),
				'modal_content' => $data_command_modal,
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'https://images.pexels.com/videos/34129037/analyzing-artificial-intelligence-background-black-background-design-34129037.jpeg?auto=compress&cs=tinysrgb&w=1200',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'Animated analytics dashboard artwork for the Data Command simulation',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Simulation\nRapid Response\nEvidence Workflow",
					AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE           => '20-30 mins',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO           => 'Intermediate',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_THREE         => 'Scenario Based',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Launch Prototype',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => 'https://geo.aiddata.org/',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_LABEL      => 'Watch Teaser',
					AidData_Home_Catalog_Meta_Boxes::META_TRAILER_URL        => 'https://videos.pexels.com/video-files/34129037/14471988_2560_1440_30fps.mp4',
					AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL         => 'Mission Brief',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_TITLE        => 'Data Command',
					AidData_Home_Catalog_Meta_Boxes::META_MODAL_SUBTITLE     => 'A rapid-response simulation for teams coordinating decisions from live financial, geospatial, and media signals.',
				),
			),
			array(
				'seed_key'      => 'decoding-china-debt-contracts',
				'slug'          => 'decoding-china-debt-contracts',
				'title'         => 'Decoding China\'s Debt Contracts',
				'excerpt'       => 'Investigating Chinese loan contract terms, conditions, and patterns across countries and sectors with Ameya Joshi.',
				'menu_order'    => 40,
				'terms'         => array( 'interview' ),
				'modal_content' => '',
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/How_China_Lends&_Collateralizes_interview.png',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'How China Lends Interview',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Interview\nSovereign Finance",
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Watch Interview',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => '/t-h/decoding-china-debt-contracts/',
				),
			),
			array(
				'seed_key'      => 'credit-evaluation-tool',
				'slug'          => 'credit-evaluation-tool',
				'title'         => 'Credit Evaluation Tool',
				'excerpt'       => 'Compare lender terms, stress-test repayment scenarios, and benchmark financing packages.',
				'menu_order'    => 50,
				'terms'         => array( 'tools' ),
				'modal_content' => $credit_tool_modal,
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/credit_shopper.png',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'Credit Evaluation Tool',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Tool\nSovereign Finance\nDecision Support",
					AidData_Home_Catalog_Meta_Boxes::META_STAT_ONE           => 'Interactive',
					AidData_Home_Catalog_Meta_Boxes::META_STAT_TWO           => 'All Levels',
					AidData_Home_Catalog_Meta_Boxes::META_INFO_LABEL         => 'Learn More',
				),
			),
			array(
				'seed_key'      => 'harboring-global-ambitions',
				'slug'          => 'harboring-global-ambitions',
				'title'         => 'Harboring Global Ambitions',
				'excerpt'       => 'Using development finance data to analyze military expansion with Alex Wooley.',
				'menu_order'    => 60,
				'terms'         => array( 'interview' ),
				'modal_content' => '',
				'meta'          => array(
					AidData_Home_Catalog_Meta_Boxes::META_SHOW_ON_FRONT_PAGE => '1',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_PATH         => 'assets/images/harboring_global_ambitions_interview.png',
					AidData_Home_Catalog_Meta_Boxes::META_IMAGE_ALT          => 'Harboring Global Ambitions',
					AidData_Home_Catalog_Meta_Boxes::META_BADGES             => "Interview\nNational Security",
					AidData_Home_Catalog_Meta_Boxes::META_CTA_LABEL          => 'Watch Interview',
					AidData_Home_Catalog_Meta_Boxes::META_CTA_URL            => '/t-h/harboring-global-ambitions/',
				),
			),
		);
	}

	/**
	 * Insert a missing seed card or mark an existing one as managed by a seed key.
	 *
	 * @param array<string, mixed> $card Seed card definition.
	 */
	private static function upsert_default_card( array $card ): void {
		$existing_id = self::find_existing_card_id( $card );

		if ( $existing_id > 0 ) {
			if ( ! metadata_exists( 'post', $existing_id, self::SEED_META_KEY ) ) {
				update_post_meta( $existing_id, self::SEED_META_KEY, (string) $card['seed_key'] );
			}

			return;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => AidData_Home_Catalog_Post_Type::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => (string) $card['title'],
				'post_name'    => (string) $card['slug'],
				'post_excerpt' => (string) $card['excerpt'],
				'post_content' => (string) $card['modal_content'],
				'menu_order'   => (int) $card['menu_order'],
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return;
		}

		update_post_meta( $post_id, self::SEED_META_KEY, (string) $card['seed_key'] );

		foreach ( $card['meta'] as $meta_key => $meta_value ) {
			if ( '' === $meta_value ) {
				continue;
			}

			update_post_meta( $post_id, $meta_key, $meta_value );
		}

		wp_set_object_terms( $post_id, $card['terms'], AidData_Home_Catalog_Post_Type::TAXONOMY, false );
	}

	/**
	 * Find an existing card by seed key first, then by slug for older inserts.
	 *
	 * @param array<string, mixed> $card Seed card definition.
	 */
	private static function find_existing_card_id( array $card ): int {
		$seed_key = (string) $card['seed_key'];
		$slug     = (string) $card['slug'];

		$existing_ids = get_posts(
			array(
				'post_type'      => AidData_Home_Catalog_Post_Type::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => self::SEED_META_KEY,
						'value' => $seed_key,
					),
				),
			)
		);

		if ( ! empty( $existing_ids ) ) {
			return (int) $existing_ids[0];
		}

		$existing = get_page_by_path( $slug, OBJECT, AidData_Home_Catalog_Post_Type::POST_TYPE );
		if ( $existing instanceof WP_Post ) {
			return (int) $existing->ID;
		}

		return 0;
	}
}
