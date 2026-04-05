<?php
include('includes/db.php');
include('includes/header.php');
include('includes/navbar.php');
?>

<section class="hero">
    <div class="container hero-wrapper">
        <div class="hero-text">
            <h1>Hire Expert Freelancers for Real-World Projects</h1>
            <p>
                SkillBridge helps businesses find top freelancers in web development,
                design, marketing, writing, and more. Build faster with trusted talent.
            </p>

       <div class="hero-buttons">
    <a href="<?php echo url('freelancers.php'); ?>" class="primary-btn">Hire a Freelancer</a>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer'): ?>
        <a href="<?php echo url('freelancer/browse-jobs.php'); ?>" class="secondary-btn">Explore Jobs</a>
    <?php else: ?>
        <a href="<?php echo url('jobs.php'); ?>" class="secondary-btn">Explore Jobs</a>
    <?php endif; ?>
</div>

            <div class="hero-stats">
                <div>
                    <h3>10K+</h3>
                    <p>Freelancers</p>
                </div>
                <div>
                    <h3>5K+</h3>
                    <p>Projects Posted</p>
                </div>
                <div>
                    <h3>98%</h3>
                    <p>Client Satisfaction</p>
                </div>
            </div>
        </div>

        <div class="hero-card">
            <h3>Popular Services</h3>
            <ul>
                <li>Website Design & Development</li>
                <li>UI/UX Design for Apps</li>
                <li>SEO and Digital Marketing</li>
                <li>Content Writing & Copywriting</li>
                <li>Video Editing & Animation</li>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Popular Categories</h2>
            <p>
                Discover top freelance categories designed for startups, businesses,
                and growing brands.
            </p>
        </div>

        <div class="categories-grid">
            <div class="category-card">
                <h3>Web Development</h3>
                <p>Frontend, backend, PHP, full-stack solutions.</p>
            </div>
            <div class="category-card">
                <h3>UI/UX Design</h3>
                <p>Modern interfaces, dashboards, apps, and landing pages.</p>
            </div>
            <div class="category-card">
                <h3>Content Writing</h3>
                <p>Blogs, website copy, product descriptions, and SEO content.</p>
            </div>
            <div class="category-card">
                <h3>Digital Marketing</h3>
                <p>SEO, social media, paid ads, and growth strategy.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#f1f5f9;">
    <div class="container">
        <div class="section-title">
            <h2>Featured Freelancers</h2>
            <p>Work with highly rated professionals across different domains.</p>
        </div>

        <div class="cards-grid">

            <?php
            $sql = "SELECT users.id, users.full_name, users.profile_image,
                           freelancer_profiles.title, freelancer_profiles.hourly_rate
                    FROM users
                    LEFT JOIN freelancer_profiles ON users.id = freelancer_profiles.user_id
                    WHERE users.role = 'freelancer'
                    ORDER BY users.created_at DESC
                    LIMIT 3";

            $result = $conn->query($sql);
            ?>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($freelancer = $result->fetch_assoc()): ?>

                    <div class="profile-card">

                        <img src="<?php echo !empty($freelancer['profile_image']) ? url('uploads/profiles/' . $freelancer['profile_image']) : url('assets/images/defaults/avatar.jpg'); ?>" class="card-img">

                        <h3><?php echo htmlspecialchars($freelancer['full_name']); ?></h3>

                        <p class="role">
                            <?php echo htmlspecialchars($freelancer['title'] ?: 'Freelancer'); ?>
                        </p>

                        <div class="profile-meta">
                            <span>$<?php echo htmlspecialchars($freelancer['hourly_rate'] ?: '0'); ?>/hr</span>
                        </div>

                        <a href="<?php echo url('freelancer-profile.php?id=' . $freelancer['id']); ?>" class="small-btn">
                            View Profile
                        </a>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No freelancers found.</p>
            <?php endif; ?>

        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Latest Jobs</h2>
            <p>Explore recent opportunities posted by clients on SkillBridge.</p>
        </div>

        <div class="cards-grid">
            <?php
            $jobsSql = "SELECT id, job_title, budget, experience_level, category
                        FROM jobs
                        WHERE status = 'open'
                        ORDER BY created_at DESC
                        LIMIT 3";
            $jobsResult = $conn->query($jobsSql);
            ?>

            <?php if ($jobsResult && $jobsResult->num_rows > 0): ?>
                <?php while ($job = $jobsResult->fetch_assoc()): ?>
                    <div class="profile-card">
                        <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                        <p class="role"><?php echo htmlspecialchars($job['category']); ?></p>
                        <p class="desc">
                            <?php echo htmlspecialchars($job['experience_level']); ?> ·
                            Budget: <?php echo htmlspecialchars($job['budget']); ?>
                        </p>

                        <a href="<?php echo url('job-details.php?id=' . $job['id']); ?>" class="small-btn">
                            View Job
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" style="background:#f8fafc;">
    <div class="container">
        <div class="section-title">
            <h2>Featured Services</h2>
            <p>Browse professional services created by freelancers on the platform.</p>
        </div>

        <div class="cards-grid">
            <?php
            $servicesSql = "SELECT id, title, price, delivery_time, image
                            FROM services
                            ORDER BY created_at DESC
                            LIMIT 3";
            $servicesResult = $conn->query($servicesSql);
            ?>

            <?php if ($servicesResult && $servicesResult->num_rows > 0): ?>
                <?php while ($service = $servicesResult->fetch_assoc()): ?>
                    <div class="profile-card">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo url('uploads/services/' . $service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="card-img">
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <p class="role"><?php echo htmlspecialchars($service['delivery_time']); ?></p>
                        <p class="desc">Price: <?php echo htmlspecialchars($service['price']); ?></p>

                        <a href="<?php echo url('service-details.php?id=' . $service['id']); ?>" class="small-btn">
                            View Service
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No services found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>How SkillBridge Works</h2>
            <p>
                A simple and professional workflow for both clients and freelancers.
            </p>
        </div>

        <div class="categories-grid">
            <div class="category-card">
                <h3>1. Post or Explore</h3>
                <p>Clients post jobs and freelancers browse opportunities or services.</p>
            </div>
            <div class="category-card">
                <h3>2. Connect</h3>
                <p>Freelancers submit proposals and clients review profiles, bids, and skills.</p>
            </div>
            <div class="category-card">
                <h3>3. Collaborate</h3>
                <p>Both sides communicate through dashboards and project-focused messages.</p>
            </div>
            <div class="category-card">
                <h3>4. Grow</h3>
                <p>Complete projects, build trust, and expand long-term freelance relationships.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#f8fafc;">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose SkillBridge</h2>
            <p>
                Built for clarity, professionalism, and future real-world expansion.
            </p>
        </div>

        <div class="cards-grid">
            <div class="profile-card">
                <h3>Professional UI</h3>
                <p class="desc">
                    Modern layouts, dashboards, tables, and workflow-based screens designed to feel like a real product.
                </p>
            </div>

            <div class="profile-card">
                <h3>Role-Based Flow</h3>
                <p class="desc">
                    Separate experiences for clients, freelancers, and administrators make the platform realistic.
                </p>
            </div>

            <div class="profile-card">
                <h3>Future-Ready Structure</h3>
                <p class="desc">
                    The project structure is ready for database integration, authentication, deployment, and scaling.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Build with Top Freelance Talent?</h2>
        <p>
            Join SkillBridge and connect with professionals who can turn your ideas
            into real digital products.
        </p>
        <?php if (isset($_SESSION['user_id'])): ?>

    <?php if ($_SESSION['role'] === 'client'): ?>
        <a href="<?php echo url('client/dashboard.php'); ?>" class="primary-btn">Go to Dashboard</a>

    <?php elseif ($_SESSION['role'] === 'freelancer'): ?>
        <a href="<?php echo url('freelancer/dashboard.php'); ?>" class="primary-btn">Go to Dashboard</a>

    <?php else: ?>
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="primary-btn">Admin Panel</a>
    <?php endif; ?>

<?php else: ?>
    <a href="<?php echo url('register.php'); ?>" class="primary-btn">Get Started Now</a>
<?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>