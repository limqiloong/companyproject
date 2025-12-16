<?php
// Mining projects page uses the shared layout; mining-specific content is injected here.
$category = 'mining';
$hideEmptySections = true;
$disableProjectLists = true;

$customMiningContent = <<<HTML
<div class="container">
<div class="mining-overview">
    <div class="section-title">Current Mining Projects</div>
    <div class="strategy-grid">
        <div class="strategy-card">
            <h4><i class="fas fa-gem"></i> Tin Mining</h4>
            <p>Muadzam Shah, Pahang — 200 MT current output, 2,400 MT target in 2025 across four new locations. Production adjusts with market demand.</p>
        </div>
        <div class="strategy-card">
            <h4><i class="fas fa-cubes"></i> Manganese Mining</h4>
            <p>100 acres at Pekan, Pahang — Estimated reserve: 200,000 MT. Operations planned to commence 2025.</p>
        </div>
        <div class="strategy-card">
            <h4><i class="fas fa-layer-group"></i> Iron Mining</h4>
            <p>Two potential mines: 100 hectares each with 60–65% iron content. Combined reserves: ~1.5–2.0 million MT.</p>
        </div>
        <div class="strategy-card">
            <h4><i class="fas fa-mountain"></i> Stone, River Sand & Marine Sand</h4>
            <p>Supplying armour rock (1–1.5m), core rock (300–800mm), crusher run, aggregates (10mm, 20mm, 25mm), and river/marine sand.</p>
        </div>
    </div>

    <div class="history-content" style="margin-top:2rem;">
        <div class="history-intro">
            <h2>Our Achievements</h2>
            <p><?php echo SITE_NAME; ?> has demonstrated consistent and sustainable growth since its establishment. Our success is attributed to strategic project execution, operational efficiency, and strong industry demand. We have expanded operations across key regions, strengthened our financial position, and developed lasting relationships with both domestic and international stakeholders.</p>
        </div>

        <div class="history-section">
            <h2 class="section-title">Bauxite Export Statistics (2014-2025)</h2>
            <div class="stats-description">
                <p>Below illustrates the annual volume of bauxite sold from 2014 up to January 2025. <?php echo SITE_NAME; ?> has exported <strong>7.81 million metric tons</strong> since its establishment. This export was facilitated by <strong>9 units of Capesize vessel</strong> (capacity: 135,000 MT each) and <strong>131 units of handy max vessel</strong> (capacity: 40,000 – 50,000 MT each) to mainland China. Additionally, the company sold a total of <strong>2.8 million metric tons</strong> on an Ex-Port (Free on Truck) basis.</p>
            </div>
            <div class="table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Bauxite Export (MT)</th>
                            <th>Bauxite Ex-Port (MT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>2014</td><td>275,200.00</td><td>-</td></tr>
                        <tr><td>2015</td><td>4,080,242.27</td><td>1,800,000.00</td></tr>
                        <tr><td>2016</td><td>1,214,243.00</td><td>500,000.00</td></tr>
                        <tr><td>2017</td><td>1,430,311.00</td><td>500,000.00</td></tr>
                        <tr><td>2019</td><td>82,950.00</td><td>-</td></tr>
                        <tr><td>2023</td><td>200,000.00</td><td>-</td></tr>
                        <tr><td>2024</td><td>512,503.00</td><td>-</td></tr>
                        <tr><td>2025</td><td>18,952.00</td><td>-</td></tr>
                        <tr class="total-row">
                            <td><strong>TOTAL</strong></td>
                            <td><strong>7,814,401.27</strong></td>
                            <td><strong>2,800,000.00</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="history-section">
            <h2 class="section-title">Industry Recognition</h2>
            <div class="recognition-card">
                <div class="recognition-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="recognition-content">
                    <h3>Top Bauxite Exporter in Malaysia (2015)</h3>
                    <p>According to Asian Metal Network statistics, <?php echo SITE_NAME; ?> (鑫鸿资源公司) was ranked <strong>#1</strong> among the top ten bauxite exporters in Malaysia in 2015.</p>
                    <div class="recognition-details">
                        <div class="detail-box">
                            <span class="detail-label">Export Volume (2015)</span>
                            <span class="detail-value">4,000,000 MT</span>
                        </div>
                        <div class="detail-box">
                            <span class="detail-label">Market Share</span>
                            <span class="detail-value">#1 Position</span>
                        </div>
                        <div class="detail-box">
                            <span class="detail-label">Total Market (Top 10)</span>
                            <span class="detail-value">20,000,000 MT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="history-section">
            <h2 class="section-title">Company Growth & Development</h2>
            <div class="growth-timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2014-2015</div>
                    <div class="timeline-content">
                        <h3>Rapid Expansion</h3>
                        <p>In the first half of 2015, <?php echo SITE_NAME; ?> exported <strong>2 million metric tons</strong> of bauxite. The company operated <strong>four new mines</strong>, with three mines beginning extraction in the first half of 2015, and one in the second half.</p>
                        <ul>
                            <li>Established operations in Terengganu with at least <strong>2 million metric tons</strong> of bauxite reserves</li>
                            <li>Established operations in Pahang with another <strong>2 million metric tons</strong> of bauxite reserves</li>
                            <li>Peak export year with <strong>4.08 million MT</strong> exported in 2015</li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2016-2017</div>
                    <div class="timeline-content">
                        <h3>Sustainable Operations</h3>
                        <p>Despite regulatory changes in Malaysia that affected the bauxite mining industry, the company maintained steady operations and continued to serve international markets.</p>
                        <ul>
                            <li>2016: Exported <strong>1.21 million MT</strong> with <strong>500,000 MT</strong> Ex-Port sales</li>
                            <li>2017: Exported <strong>1.43 million MT</strong> with <strong>500,000 MT</strong> Ex-Port sales</li>
                            <li>Continued compliance with new mining regulations</li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2019-2025</div>
                    <div class="timeline-content">
                        <h3>Diversification & Stability</h3>
                        <p>The company has diversified its operations while maintaining a strong presence in the bauxite export market. Our solid revenue growth, healthy profit margins, and reinvestment of retained earnings have enabled us to scale operations while maintaining financial stability.</p>
                        <ul>
                            <li>Continued operations with selective mining activities</li>
                            <li>Maintained relationships with international stakeholders</li>
                            <li>Focus on sustainable development and long-term value creation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="history-section">
            <h2 class="section-title">Industry Context</h2>
            <div class="industry-info">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <h3>China's Demand</h3>
                    <p>Growth in aluminium demand has been driven mainly by strong demand from China. Malaysian bauxite proved critical for China's aluminium producers as they sought alternatives to Indonesian bauxite supplies.</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Market Position</h3>
                    <p>In 2015, China imported approximately <strong>20.5 million tons</strong> of bauxite from Malaysia (January to November). Asian Metal estimated Malaysia's total bauxite supply to China in 2015 to be around <strong>22 million tons</strong>.</p>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Regulatory Environment</h3>
                    <p>In accordance with regulations in Malaysia, mines which hadn't obtained mining permits before 31 August 2015 were subject to closure. This led to an expected reduction of at least 40% in bauxite output if strict enforcement measures were taken.</p>
                </div>
            </div>
        </div>

        <div class="history-section achievements-summary">
            <h2 class="section-title">Key Achievements Summary</h2>
            <div class="achievements-grid">
                <div class="achievement-item">
                    <div class="achievement-number">7.81M</div>
                    <div class="achievement-label">Total Bauxite Exported (MT)</div>
                    <div class="achievement-desc">Since establishment</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">#1</div>
                    <div class="achievement-label">Top Exporter (2015)</div>
                    <div class="achievement-desc">Malaysia Bauxite Exporters</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">4M</div>
                    <div class="achievement-label">Peak Export Year (2015)</div>
                    <div class="achievement-desc">Metric Tons</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">140</div>
                    <div class="achievement-label">Total Vessels Used</div>
                    <div class="achievement-desc">9 Capesize + 131 Handy Max</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">4M+</div>
                    <div class="achievement-label">Bauxite Reserves</div>
                    <div class="achievement-desc">Terengganu & Pahang</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">21+</div>
                    <div class="achievement-label">Years of Experience</div>
                    <div class="achievement-desc">In the Industry</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
HTML;

include 'projects-earthwork.php';
?>
