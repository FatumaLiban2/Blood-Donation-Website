<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Life Blood - Blood Donation Center</title>
        <link rel="stylesheet" href="views/styles/index.css">
    </head>
    <body>
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <h2>Life Blood</h2>
                </div>
                <ul class="nav-menu">
                    <li><a href="#home">Home></a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="learn">Learn</a></li>
                    <li><a href="#donate">Donate</a></li>
                    <li><a href="#hospital">Hospital</a></li>
                    <li><a href="#eligibility">Eligibility</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <div class="nav-auth">
                    <button>Login</button>
                    <button>Sign Up</button>
                </div>
            </div>
        </nav>

        <!--- Home Section --->
        <section id="home" class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Save Lives Through Blood Donations</h1>
                    <p>Your donation can save up to three lives. Join our community of heroes and make a difference today.</p>
                    <div class="hero-buttons">
                        <button class="btn-primary btn-large">Donate Now</button>
                        <button class="btn-secondary btn-large">Learn More</button>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="resources/images/blood_samples.jpeg" alt="Blood donation">
                </div>
            </div>
        </section>

        <!--- About Section --->
        <section id="about" class="about">
            <div class="container">
                <div class="section-header">
                    <h2>About Life Blood</h2>
                    <p>Connecting donors with those in need.</p>
                </div>
                <div class="about-content">
                    <div class="about-text">
                        <h3>Our Mission</h3>
                        <p>At LifeBlood, our mission is to create a seamless bridge between generous blood donors and healthcare facilities in critical need. We believe that every drop of blood donated has the power to save lives and restore hope to families in their darkest moments.</p>

                        <h3>Our Goal</h3>
                        <p>Our primary goal is to maintain a reliable, safe, and accessible blood supply for our community. We strive to educate the public about the importance of blood donation, eliminate barriers to giving, and ensure that no patient goes without the blood products they need for recovery.</p>

                        <h3>Why We Exist</h3>
                        <p>Every 2 seconds, someone in our country needs blood. Whether it's for emergency surgeries, cancer treatments, or chronic illnesses, blood donations are essential for saving lives. LifeBlood exists to make the donation process simple, safe, and rewarding for donors while maintaining the highest standards of care and safety.</p>

                        <div class="about-img">
                            <img src="resources/images/together.jpeg" alt="Helping hands" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--- Learn Section --->
        <section id="learn" class="learn">
            <div class="container">
                <div class="section-header">
                    <h2>Learn About Blood Donation</h2>
                    <p>Understanding blood types and the donation process</p>
                </div>

                <div class="learn-content">
                    <div class="blood-types">
                        <h3>Blood Types Explained</h3>
                        <div class="blood-types-grid">
                            <div class="blood-type-card">
                                <h4>Type O-</h4>
                                <p><strong>Universal Donor</strong></p>
                                <p>Can donate to all blood types. Only 6.6% of the population has O- blood.</p> 
                            </div>
                            <div class="blood-type-card">
                                <h4>Type o+</h4>
                                <p><strong>Most Common</strong>
                                <p>Can donato to all positive blood types. About 37.4% of the population has O+ blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type A-</h4>
                                <p><strong>Rare Donor</strong></p>
                                <p>Can donate to A- and AB- blood types. Only 6.3% of the population has A- blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type A+</h4>
                                <p><strong>Common Donor</strong></p>
                                <p>Can donate to A+ and AB+ blood types. About 35.7% of the population has A+ blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type B-</h4>
                                <p><strong>Rare Donor</strong></p>
                                <p>Can donate to B- and AB- blood types. Only 1.5% of the population has B- blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type B+</h4>
                                <p><strong>Less Common</strong></p>
                                <p>Can donate to B+ and AB+ blood types. About 8.5% of the population has B+ blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type AB-</h4>
                                <p><strong>Universal Recipient</strong></p>
                                <p>Can donate to AB- blood type only. Only 0.6% of the population has AB- blood.</p>
                            </div>
                            <div class="blood-type-card">
                                <h4>Type AB+</h4>
                                <p><strong>Universal Recipient</strong></p>
                                <p>Can receive from all blood types. About 3.4% of the population has AB+ blood.</p>
                            </div>
                        </div>
                    </div>

                    <div class="donation-process">
                        <h3>How Blood Donation Works</h3>
                        <div class="process-image">
                            <img src="/resources/images/process.jpeg" alt="Blood testing laboratory" /> 
                        </div>
                        <div class-name="process-steps">
                            <div class="step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h4>Registration and Health Screening</h4>
                                    <p>Doonors complete a a brief health questionaire and mini-physical examination including blood pressure, pulse, temperature, and hemoglobin checks</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h4>The Donation Process</h4>
                                    <p>The actual donation process takes about 10-15 minutes. A sterile needle is used to collect approximately one pint of blood while relaxing in a comfort chair.</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h4>Rest & Refreshment</h4>
                                    <p>After donating, donors are encouraged to rest for a few minutes and enjoy light refreshments to help replenish fluids and energy. Our staff monitors you to ensure you feel well before leaving.</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h4>Testing and Processing</h4>
                                    <p>Your blood is sent to a lab where it is tested for various infectious diseases and blood type. It is then separated into components (red cells, plasma, platelets) to maximize its use.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--- Blood Donation Section --->
        <section id="donate" class="donate">
            <div class="container">
                <div class="section-header">
                    <h2>Donate Blood Today</h2>
                    <p>Shedule your donation appointment and save lives</p>
                </div>
                <div class="donate-content">
                    <div class="donate-form">
                        <h3>Shedule Your Appointment</h3>
                        <form id="donationForm">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullname" name="fullName" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="bloodType">Blood Type (if known)</label>
                                <select id="bloodType" name="bloodType">
                                    <option value="">Select Blood Type</option>
                                    <option value="O-">O-</option>
                                    <option value="O+">O+</option>
                                    <option value="A-">A-</option>
                                    <option value="A+">A+</option>
                                    <option value="B-">B-</option>
                                    <option value="B+">B+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="AB+">AB+</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferedDate">Preferred Date</label>
                                <input type="date" id="preferedDate" name="preferedDate" required>
                            </div>
                            <div class="form-group">
                                <label for="preferedTime">Preferred Time</label>
                                <select id="preferedTime" name="preferedTime" required>
                                    <option value="">Select Time</option>
                                    <option value="9:00 AM">9:00 AM</option>
                                    <option value="10:00 AM">10:00 AM</option>
                                    <option value="11:00 AM">11:00 AM</option>
                                    <option value="12:00 PM">12:00 PM</option>
                                    <option value="2:00 PM">2:00 PM</option>
                                    <option value="3:00 PM">3:00 PM</option>
                                    <option value="4:00 PM">4:00 PM</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primary btn-large">Shedule Appointment</button>
                        </form>
                    </div>
                    <div class="donate-info">
                        <h3>Why Your Donation Matters</h3>
                        <div class="impact-stats">
                            <div class="stat">
                                <h4>3</h4>
                                <p>Lives saved per donation</p>
                            </div>
                            <div class="stat">
                                <h4>38,000</h4>
                                <p>Units of blood needed daily</p>
                            </div>
                            <div class="stat">
                                <h4>56</h4>
                                <p>Days between donations</p>
                            </div>
                        </div>
                    </div>
                    <div class="donations-benefits">
                        <h4>Benefits of Donating:</h4>
                        <ul>
                            <li>Free health screening</li>
                            <li>Burn calories about 650 calories per donation</li>
                            <li>Improve cardiovascular health</li>
                            <li>Feel good by helping others</li>
                            <li>Free blood type testing</li>
                            <li>Community impact</li>
                            <li>Free refreshments</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!--- Hospital Section --->
        <section id="hospital" class="hospital">
            <div class="container">
                <div class="section-header">
                    <h2>Our Hospital Network</h2>
                    <p>State of the art facilities serving our community</p>
                </div>
                <div class="hospital-content">
                    <div class="hospital-image">
                        <img src="resources/images/team.jpeg" alt="Hospital Facility"/>
                    </div>
                    <div class="hospital-info">
                        <h3>LifeBlood Medical Center</h3>
                        <p>Our flagship facility features cutting-edge medical technology and a team of dedicated healthcare professionals committed to providing the highest quality care.</p>

                        <div class="hospital-features">
                            <div class="feature">
                                <h4>Modern Equipment</h4>
                                <p>Latest blood collection and processing technology ensuring safety and efficiency.</p>
                            </div>
                            <div class="feature">
                                <h4>Expert Staff</h4>
                                <p>Certified phlebotomists and medical professionals with years of experience.</p>
                            </div>
                            <div class="feature">
                                <h4>Comfortable Environment</h4>
                                <p>Relaxing donation areas designed with donor comfort in mind.</p>
                            </div>
                            <div class="feature">
                                <h4>Safety First</h4>
                                <p>Strict adherence to FDA guidelines and safety protocols.</p>
                            </div>
                        </div>

                        <div class="hospital-stats">
                            <div class="stat">
                                <h4>50,000+</h4>
                                <p>Annual donations collected</p>
                            </div>
                            <div class="stat">
                                <h4>24/7</h4>
                                <p>Emergency blood supply</p>
                            </div>
                            <div class="stat">
                                <h4>15+</h4>
                                <p>Years serving the community</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </section>

        <!--- Eligibility Section --->
        <section id="eligibility" class="eligibility">
            <div class="container">
                <div class="section-header">
                    <h2>Donation Eligibility</h2>
                    <p>Check if you are eligible to donate blood</p>
                </div>
                <div class="checker-form">
                    <div class="question">
                        <label>Are you at least 17 years old?</label>
                        <div class="radio-group">
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>