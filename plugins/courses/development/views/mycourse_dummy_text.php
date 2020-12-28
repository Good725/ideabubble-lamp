<?php // Introduction?>

    <?php $subsections = []; ?>
    <?php ob_start(); ?>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

        <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>

        <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>

    <?php $subsections[] = ['title' => 'Introduction', 'duration' => 15, 'type' => 'text', 'content' => ob_get_clean(), 'complete' => 0]; ?>

    <?php
    $sections[] = ['title' => 'Introduction', 'duration' => 15, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Risk analysis summary ?>

    <?php $subsections = []; ?>
    <?php ob_start(); ?>
        <p>Before looking in depth at the causes and consequences of loss it will be useful, for context purposes, to briefly summarise the risk analysis process. The full risk analysis process is covered in other sections.</p>

        <p>Risk analysis is carried out to identify what may be lost from threats. It is completed before any decisions can be considered on loss prevention measures or threat countermeasures. In simple language, security risk management can be broken down into two main stages or phases, the problems and the solutions.</p>

        <p>The risk analysis process can be described as Phase 1, looking at the problems, for example:</p>

        <dl class="dl-flex">
            <dt>What is at risk</dt>
            <dd>The assets at risk</dd>

            <dt>At risk from what</dt>
            <dd>The threats such as theft or fire</dd>

            <dt>Because of</dt>
            <dd>The vulnerabilities such as weak procedures</dd>

            <dt>How likely is an event</dt>
            <dd>The probability or likelihood of an incident</dd>

            <dt>he consequences</dt>
            <dd>The financial or reputational impact on the client</dd>
        </dl>

        <p>Phase 2 would focus on the solutions, for example:</p>

        <dl class="dl-flex">
            <dt>Recommendations</dt>
            <dd>Measures and alternative measures</dd>

            <dt>Cost benefit analysis</dt>
            <dd>Cost and feasibility of measures and alternatives</dd>

            <dt>Security plan</dt>
            <dd>Agreed, implemented, managed and reviewed</dd>
        </dl>

        <p>The remainder of this chapter will focus on the causes and consequences of loss, which are critical elements of the security risk assessment process.</p>

    <?php $subsections[] = ['title' => 'Risk analysis summary', 'duration' => 120, 'type' => 'text', 'content' => ob_get_clean(), 'complete' => 0]; ?>

    <?php
    $sections[] = ['title' => 'Risk analysis summary', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Defining client activity ?>

    <?php ob_start(); ?>
        <p>While each individual client and each premises is unique; it is possible and helpful to have an overview of the environments in which the assessor may operate. Typically there are three primary environments in which assessors may find themselves providing advice.</p>

        <p>These are the:</p>

        <ul style="margin-left: 1em;">
            <li>State sector</li>
            <li>Private sector</li>
            <li>VIP sector</li>
        </ul>
    <?php $subsections[] = ['title' => 'Defining the client', 'type' => 'text', 'duration' => 30, 'content' => ob_get_clean(), 'complete' => 0]; ?>

    <?php ob_start(); ?>
        <p>These include government or state bodies such as government departments, state and semi-state agencies and civic services. There is an obligation on the State to safeguard the citizens and assets of the state against loss or harm, certain State assets and functions are subject to legislation. Increasingly the private sector is taking on more of the functions of state services; however, the responsibility still rests with the state.</p>
    <?php $subsections[] = ['title' => 'The state sector', 'type' => 'text', 'duration' => 30, 'content' => ob_get_clean(), 'complete' => 0]; ?>

    <?php ob_start(); ?>
        <p>This is the business community or commercial sector, organisations privately owned by an individual or group of individuals. Safeguarding privately owned assets against loss. The business community also has obligations to safeguard people such as employees and visitors.</p>
    <?php $subsections[] = ['title' => 'The private sector', 'type' => 'text', 'duration' => 20, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>VIP means very-important person; this includes anyone with special status or privilege who may be at personal risk. These include individual persons such as business executives, high-ranking officials, Government Ministers and those classed as celebrities.</p>

        <p>This is a unique sector as it involves safeguarding private individuals, sometimes including their families, from bodily harm.</p>

        <p>Within these three broad sectors there are then a number of unique sub sectors or categories, which then lead on to core service or business areas.<br />
           A professional security risk assessor is not a salesperson promoting a certain approach or product; they are independent advisers capable of operating across a broad spectrum of environments.</p>

        <p>While each individual location or premises are different, an added consequence of looking at core services and business areas highlights the fact that these differing areas have their own unique threats and vulnerabilities.</p>

        <p>Risks associated with a children’s hospital will be unique and not comparable with risks associated with a bank branch.</p>

        <p>The next section looks, in no particular order, at breaking down these sectors by category and then by core service or business area.</p>
    <?php $subsections[] = ['title' => 'The VIP sector', 'type' => 'text', 'duration' => 120, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Defining client activity', 'duration' => 240, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Defining assets ?>
    <?php ob_start(); ?>
    <p>Within security risk management, the term asset is used to describe what is at risk. This is also very broad as assets can be anything of value to an individual or organisation.</p>

    <p>Buildings, vehicles and equipment are tangible assets.</p>

    <p>Intangible assets include staff, work procedures and a good reputation.</p>

    <?php $subsections[] = ['title' => 'Defining the client', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Defining assets', 'duration' => 60, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>


<?php // Defining loss ?>
    <?php ob_start(); ?>
    <p>The picture emerging from defining the sectors and services provides indicators then as to what may be lost or what is at risk. The term loss is very broad, it means failure to keep or continue to have something, the definition is expanded to loss of a person (death), a possession (theft) or property (fire), it can also be loss of:</p>

    <dl class="dl-flex">
        <dt>Time</dt>
        <dd>Lost production downtime, service time, delivery time</dd>

        <dt>Ability</dt>
        <dd>Unable to process goods, workflow disrupted</dd>

        <dt>Confidence</dt>
        <dd>Resulting from mistakes, incompetence</dd>

        <dt>Reputation</dt>
        <dd>Impacting on a good name and integrity</dd>
    </dl>

    <p>While a lot of what is lost can be replaced or compensated for, it cannot be recovered. A building destroyed by fire can be replaced, possessions stolen can be compensated for but the legacy value of the original is lost forever. Loss of profit in a particular year forms part of the permanent history of an organisation.</p>

    <p>Losses can be tangible or intangible, loss of assets (goods, processes or people) can be seen and the impact felt immediately, loss of reputation could be more difficult to measure the impact of immediately.</p>

    <p>Poor services lead to loss of reputation over time, loss of business through loss of confidence can slowly evolve over time while customers gradually find alternatives.</p>

    <?php $subsections[] = ['title' => 'Defining loss', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>While the process of identifying in detail what is at risk in the business environment requires an assessment of each individual client and premises, there are broad areas of loss or general headings under which losses can begin to be more clearly defined, for example;</p>

        <dl class="dl-flex">
            <dt>Finished Products</dt>
            <dd>Full value goods ready for sale</dd>

            <dt>Records</dt>
            <dd>Client records, company records, contracts</dd>

            <dt>Information Technology / ICT</dt>
            <dd>Processes, data base, secrets, formula, third party information and process storage and management systems</dd>

            <dt>Cash and Valuables</dt>
            <dd>Full value items - money, bonds, art - priceless / 	irreplaceable</dd>

            <dt>Reputation</dt>
            <dd>Good name, brand, history, standing, status, reliable</dd>

            <dt>Integrity</dt>
            <dd>Honest, legal, compliance, environment, safety</dd>

            <dt>Personnel</dt>
            <dd>Important staff, key, executives, legacy experience</dd>

            <dt>Plant and Machinery</dt>
            <dd>Vital for processing or manufacturing</dd>

            <dt>Tools and Equipment</dt>
            <dd>Office equipment / computers, safety equipment</dd>

            <dt>Buildings / Property</dt>
            <dd>Where the work is carried out, permanent and temporary</dd>

            <dt>Raw Materials</dt>
            <dd>Required to make the finished product, production</dd>

            <dt>Process and Services</dt>
            <dd>The ability to carry out, respond to customer needs, time</dd>

            <dt>Customer Property</dt>
            <dd>In storage, being delivered, held, coats / bags</dd>

            <dt>Energy</dt>
            <dd>Waste or theft of fuel, misuse or abuse</dd>

            <dt>Vehicles</dt>
            <dd>Fork lift, truck, van, car</dd>

            <dt>Waste Material</dt>
            <dd>Skip, environmental, controlled waste, shrinkage</dd>
        </dl>

        <p>The process of asset identification can be more clearly carried out using these headings.</p>

    <?php $subsections[] = ['title' => 'General headings', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Defining loss', 'duration' => 60, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Crime-related causes of loss ?>
    <?php ob_start(); ?>
        <p>While not the only cause, crime is a major factor in loss prevention and security risk management. While assessors are not expected to be experts in criminal law, an understanding of the different types of crime and crime terminology is beneficial. The depth of this understanding is limited to relevant crimes and avoids the more extreme such as murder. The following therefore is a brief description of the more common crimes and terms; this is a broad overview using common definitions of each term.</p>

        <p>These definitions should be viewed as general descriptions, some of the terms used may not be identical in all jurisdictions and some of the definitions may also vary. Candidates should use this as a source of and understand the general descriptions, ensuring that whatever way the particular crime is described in their jurisdiction is correct.</p>

    <?php $subsections[] = ['title' => 'Introduction', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <h3>Crime</h3>
        <p>Crime is defined as <i>“a wrongful act which directly and seriously threatens the security and well being of society. It is a wrongful act against the community with punishment imposed by the courts and enforced by the executive (Government)”</i></p>

        <h3>A criminal</h3>
        <p>Somebody who has committed a crime.</p>

        <h3>Theft</h3>
        <p>A person is guilty of theft if he or she dishonestly appropriates property without the consent of its owner and with the intention of depriving its owner of it. Other actions or offences fall broadly into the theft category such as Larceny, Shoplifting, Pilferage and Embezzlement. Losses through theft can be linked to robbery, burglary, deception and espionage. Espionage can be described as loss of information or intelligence. Theft can also be defined as internal theft or staff theft.</p>

        <h3>Deception</h3>

        <p>There are number of offences, which can be put under the heading of deception such as Fraud, Forgery and Counterfeiting.</p>

        <h3>Trespass</h3>

        <p>To go onto somebody else’s land or enter somebody else’s property without permission. The act of trespass leads to, or includes another act that results in loss.</p>

        <h3>Burglary</h3>

        <p>A person is guilty of burglary if he or she enters any building or part of a building as a trespasser with intent to commit an arrestable offence, or, having entered any building or part of a building as a trespasser, commits or attempts to commit any such offence therein.</p>

        <h3>Robbery</h3>

        <p>A person is guilty of robbery if he or she steals, and immediately before or at the time of doing so, and in order to do so, uses force on any person or puts or seeks to put any person in fear of being there and then subjected to force.</p>

        <h3>Criminal Damage to Property</h3>

        <p>To damage, destroy, deface, dismantle, render irreparable or unfit for use, prevent or impair the operation of any property.</p>

        <h3>Corruption</h3>

        <p>Officials within public bodies taking bribes.</p>

        <h3>Extortion</h3>

        <p>Obtaining something by using force or threats.</p>

        <h3>Arson</h3>

        <p>The burning of a building or property for a criminal or malicious reason.</p>

        <h3>Vandalism</h3>

        <p>The malicious and deliberate defacement or destruction of somebody else’s property.</p>

        <h3>Sabotage</h3>

        <p>The deliberate damaging or destroying of property or equipment, undermining or destroying somebody’s efforts or achievements.</p>

        <h3>Kidnapping</h3>

        <p>The action or crime of forcefully taking away and holding somebody prisoner, usually for ransom.</p>

        <h3>Assault</h3>

        <p>A person shall be guilty of the offence who, without lawful excuse, intentionally or recklessly, directly or indirectly applies force to or causes an impact on the body of another or causes another to believe on reasonable grounds that he or she is likely immediately to be subjected to any force or impact.</p>

        <h3>Espionage</h3>

        <p>The use of surveillance, spying or spy’s to gather secret information or intelligence.</p>

        <h3>Other Common Terminology</h3>

        <p>Terms such as Product Tampering, Mugging, Pick Pocket, Break-in, Shoplifting and Cyber-Crime are commonly used, this type of terminology generally describes how a crime was committed. These actions or events will lead back to a primary piece of legislation such as theft, fraud or burglary.</p>

        <p>Another example is White Collar crime, this is a term used to describe crimes carried by management level personnel, traditionally Blue Collar or operational level employees were deemed to be the most likely contributors to loss within companies. This has changed to more sophisticated forms of crime perpetrated by white collar or management personnel; it still however leads back to theft or fraud offences.</p>

        <p>Safeguarding human life will always take precedence over loss of property, therefore those actions, which impact on the safety of individuals as opposed to protecting property, will always take priority.</p>

        <h3>Terrorism and Extremism</h3>

        <p>The terms Terrorist, Extremist or Activist describe a type of person motivated to carry out crimes based on certain values, beliefs or ideologies as opposed to any personal or private financial gain. These actions tend to be focused primarily against Government or broader society.</p>

        <p>Acts of terrorism can cover a range of criminal offences, the most extreme being explosion, arson and murder.</p>

        <h3>Civil Law</h3>

        <p>Civil wrongs (the laws of Tort) are generally private disputes between individuals rather that any act against society. Defamation (Libel and Slander) and Nuisance are Torts. Negligence is perhaps the most relevant Tort as it covers areas such as duty of care, safe place of work, defective products, reckless disregard and vicarious liability. Trespass to the person, trespass to land and false imprisonment are torts, but the same incident can also lead to criminal prosecution.</p>

    <?php $subsections[] = ['title' => 'Definitions', 'type' => 'text', 'duration' => 240, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Crime-related causes of loss', 'duration' => 300, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Other human factor causes of loss ?>
    <?php ob_start(); ?>
        <p>Human factor causes include errors, accidents, mistakes or other actions, while not deliberate or malicious or planned criminal acts they can lead to loss.</p>

        <p>For example:</p>

        <ul style="margin-left: 1em;">
            <li>Chemical spill</li>
            <li>Nuclear accident</li>
            <li>Fumes</li>
            <li>Oil spill</li>
            <li>Gas leak</li>
            <li>Explosion</li>
            <li>Fire</li>
            <li>Error</li>
            <li>Equipment failure</li>
            <li>System / process failure</li>
            <li>Pollution</li>
            <li>Power failure</li>
        </ul>

    <?php $subsections[] = ['title' => 'Overview', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>Waste is a unique feature. All waste products must be managed and disposed of properly. By definition, waste has no apparent value to the organisation but environment laws insist that all waste is controlled; therefore, waste is a cost factor as poor control leads to loss.</p>
    <?php $subsections[] = ['title' => 'Waste', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Other human factor causes of loss', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset for the next group
    ?>

<?php // Natural causes of loss ?>
    <?php ob_start(); ?>
        <p>Natural causes can be viewed as those with no human involvement, therefore no criminal intent. Natural causes can be broken down into two categories weather and environmental, as follows:</p>

    <?php $subsections[] = ['title' => 'Types', 'type' => 'text', 'duration' => 20, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>Weather is the state of the atmosphere, such as:</p>

        <ul style="margin-left: 1em;">
            <li>Excessive rain / water / floods</li>
            <li>Excessive snow</li>
        </ul>

    <?php $subsections[] = ['title' => 'Weather', 'type' => 'text', 'duration' => 10, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>Environmental relates to the natural world and surroundings such as:</p>

        <ul style="margin-left: 1em;">
            <li>Fire</li>
            <li>Earthquake</li>
            <li>Landslide</li>
            <li>Volcano</li>
            <li>Tidal wave / tsunami</li>
        </ul>

    <?php $subsections[] = ['title' => 'Environmental', 'type' => 'text', 'duration' => 10, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>Fire is unique as it features in all categories, crime, human factor and natural causes, it can be accidental or malicious. Fire is generally considered as the biggest threat as it is potentially a devastatingly destructive force and a killer.</p>

    <?php $subsections[] = ['title' => 'Fire', 'type' => 'text', 'duration' => 20, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>An accident is defined as an unplanned happening, in the vast majority of cases accidents occur as the result of human action or human inaction, somebody does something innocently in error leading to accident or somebody does nothing, the result of which is an accident.</p>

        <p>For example, if it can be reasonably predicted that something may happen such as a fire or flood then it cannot be classed as an unplanned happening.</p>

        <p>There are exceptions such as spontaneous combustion or a once in a lifetime weather or environmental event that can be classed as unplanned and therefore unplanned for.</p>

        <p>For clarification, major incidents are those involving loss of life and substantial damage, it is the scale and consequences that define an incident as major and not the cause.</p>

    <?php $subsections[] = ['title' => 'Accidents', 'type' => 'text', 'duration' => 30, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Natural causes of loss', 'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // Losses involving employees ?>
    <?php ob_start(); ?>
        <p>Employee crime is prevalent in every type of business. Internal or staff theft is the most common crime and cause of loss involving employees.</p>

        <p>The retail sector is most vulnerable, a survey in the American retail sector showed losses through employee theft and damage to goods accounted for four times the value lost through shoplifting.</p>

        <p>Other survey results show that 95% of all businesses are affected and that one third of all bankruptcies in America cite employee theft as the primary cause of the closure.</p>

        <p>The construction industry loses tools, equipment and particularly supplies, every company suffers minor theft such as pens, paper and stationery, office materials and janitorial supplies.</p>

        <p>Theft and damage can include misuse or abuse of employer equipment and unauthorised use of facilities such as telephone or internet service.</p>

        <p>The most serious of crimes, such as arson or criminal damage, carried out by employees generally relate to a grievance of some sort. Assault can occur where an employee threatens or strikes a customer, visitor or fellow employee. This action can lead to both criminal and civil actions, resulting in both financial and reputational loss.</p>

    <?php $subsections[] = ['title' => 'Typical scenarios', 'type' => 'text', 'duration' => 120, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>The most common criminal acts leading to loss carried out by employees:</p>

        <ul style="margin-left: 1em;">
            <li>Theft</li>
            <li>Deception</li>
            <li>Criminal damage</li>
            <li>Sabotage</li>
            <li>Extortion</li>
            <li>Arson</li>
            <li>Vandalism</li>
            <li>Assault</li>
        </ul>

        <p>Collusion is a method of committing theft involving the staff member colluding with a supplier, customer, friend or fellow employee to commit an act. Providing information to others to assist them commit a robbery or fraud for example, is another form of crime.</p>
    <?php $subsections[] = ['title' => 'Common employee crimes', 'type' => 'text', 'duration' => 30, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>As with criminal acts generally, employees carry out crimes for a number of reasons. Besides the typical motivators such as basic dishonesty, revenge, need or greed, employee theft and other crimes are carried out because employees are dissatisfied or disaffected for any number of real or perceived reasons.</p>

        <p>The following is an example of some reasons cited:</p>
        <ul style="margin-left: 1em;">
            <li>The company is rich and greedy</li>
            <li>The company is perceived to be dishonest</li>
            <li>Weak management</li>
            <li>Poor procedures</li>
            <li>Temptation and opportunity</li>
            <li>Poor supervision</li>
            <li>Poor pay and conditions</li>
            <li>Unfair treatment or perceived unfair treatment</li>
            <li>An entitlement</li>
            <li>Everyone else does it</li>
            <li>It is customary</li>
            <li>The losses are insured</li>
            <li>Lack of appreciation or respect</li>
            <li>Low morale</li>
            <li>Little risk of capture or prosecution</li>
        </ul>
    <?php $subsections[] = ['title' => 'Background to Employee Crime', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>


    <?php ob_start(); ?>
        <p>Besides bankruptcy, other consequences are direct financial losses in the main, loss of productivity when supplies or equipment are involved, loss of confidence or reputation in the company by customer and loss of faith or frustration by other employees who remain honest. The poor corporate culture section further on discusses management weaknesses and other management issues, which may contribute to the problem of employee crime.</p>
    <?php $subsections[] = ['title' => 'Consequences', 'type' => 'text', 'duration' => 30, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Losses involving employees',              'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // Internal and external threats ?>
    <?php ob_start(); ?>
        <p>For clarification, internal or external has nothing to do with geography or location as in indoors or outdoors, on-site or away from site. It is internal to the client organisation or external to the client organisation. Internal threats are those in which the organisation has control or influence over such as employee theft of the organisations assets. External threats are those in which the organisation has no control or influence over, such as a robbery or a break-in.</p>

        <p>The organisation can put in systems to safeguard its own assets, or assets it has responsibility over.</p>

        <p>The organisation can put in place procedures to manage its own employees.</p>

        <p>The organisation cannot manage or control the activities of external persons such as thieves or terrorists.</p>

        <p>The public interest and the rights of individuals are more important than any organisation’s desires. While the organisation will have a clear direct interest in any event leading to loss or potential loss of its assets, it cannot infringe on the statutory rights of others. Employees are protected against any processes, which could be regarded as invasive or insidious.</p>
    <?php $subsections[] = ['title' => 'Consequences', 'type' => 'text', 'duration' => 75, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Internal and external threats',           'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // Poor corporate culture ?>
    <?php ob_start(); ?>
        <p>Not every security problem or loss risk requires major investment in security products or services. The fourth strand of a security system “procedures” is sometimes where security problems can be solved or risk significantly reduced.</p>

        <p>In this context, poor corporate culture is in itself a cause of loss or a contributor to losses.</p>

        <p>This section looks at organisation management in particular the bad habits or poor procedures within the organisation and perceptions within staff, suppliers, customers and the public.</p>

        <p>Senior management in organisations determine culture and ethos. They set and enforce the rules, management are also looked to for direction, leadership and guidance. When management fail to show leadership or when the integrity of management is questioned or questionable this affects all others around them.</p>
    <?php $subsections[] = ['title' => 'Introduction', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <p>A detailed explanation of these functions is included below. International standards such as the ISO suite of standards use the term “Top Management” in their documentation to describe this level. It is the most senior level or senior management level in an organisation and includes positions such as Chairman, Directors, Chief Operating Officer, Director of Services and Chief Executive or equivalent.</p>

        <p>Collectively the Chair, Managing Director and Directors or equivalent are part of the highest-level board of management. These high-level boards make the main decisions on their organisations direction and policy. These policy decisions are linked to the organisations ethos and vision.</p>

        <p>Policy made at the highest level has more value, provided effective communication is in place to ensure all policy decisions made, are known to those involved. Policy when made at this level should be clear and documented.</p>

        <p>The Managing Director or Chief Executive is responsible for the operational implementation of these policy decisions throughout the organisation. Operational implementation is through the organisations procedures, which must be clear, documented and made known.</p>

        <p>Positions, job descriptions and job titles can differ across various types and sizes of organisation, however the vast majority will have a designated person charged with the responsibility of managing the organisation and reporting upwards to a more senior board or chair level.</p>

        <p>Use of the term “the organisation” is in a broad sense meaning a company, partnership, institution, state or semi-state body or a government department; it has no legal meaning or description other than to avoid the use of “a Company” which has a precise legal meaning.</p>

        <p>The following is a summary of the five generally accepted main functions of senior management. Theorist and management professionals have, over time, expanded on and re-defined these headings.</p>
    <?php $subsections[] = ['title' => 'Senior-level management and functions', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Poor corporate culture', 'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // Defining an organisation's weakness ?>
    <?php ob_start(); ?>
        <p>This section looks at organisational weaknesses, lack of awareness or lack of attention to loss prevention and security within organisations. These areas, which can be directly linked back to the organisations culture, stem from a failure to understand the consequences of actions or inactions, thereby contributing to loss.</p>

        <h3>No Policies or Procedures</h3>
        <p>The area of loss prevention and security does not feature in the day-to-day management of the organisation.</p>

        <h3>Responsibility</h3>
        <p>No one designated as responsible or those designated are unsure of the role.</p>

        <h3>Management Tolerance Levels</h3>
        <p>Internal theft and equipment or property damage are all regular features but management view them as occupational hazards to be tolerated. Management are driven by profit only, as long as targets are met theft, fraud etc. are ignored and become the norm throughout the organisation. Losses are regarded as acceptable.</p>

        <h3>Vague Policies</h3>
        <p>It is unclear to others reading policies, what way management are thinking.</p>

        <h3>Ineffective Procedures</h3>
        <p>Procedures exist, but are so broad or generic they are not effective.</p>

        <h3>Flexible Procedures</h3>
        <p>Procedures exist but can be applied or not depending on certain circumstances or who is dealing with certain matters, enforced today and ignored tomorrow, not all staff need follow them.</p>

        <h3>Poor Decision Making</h3>
        <p>When it is obvious to staff, customers or suppliers that decisions needed are not made or partly made or constantly deferred. Inaction is deemed safest.</p>
        <p>Poor planning in a volatile industry is an example leading to loss of confidence in management.</p>

        <h3>Poor Quality Information</h3>
        <p>When staff, customers or suppliers are not given the amount of information required to support organisation policies or procedures. No one explains why.</p>

        <h3>Poorly Focused Information</h3>
        <p>Upwards and downwards flow of information does not exist or is not consistent or not timely. The wrong people are told first, rumour and interpretation become the main sources of information.</p>

        <h3>Management Integrity</h3>
        <p>Management honesty, ethics, personal behaviour is of a low standard.</p>

        <h3>Treatment of Staff</h3>
        <p>Unfair redundancy, unstable or temporary employment, poor terms and conditions, unfavourable or inconsistent practices are the norm, no staff loyalty.</p>

        <h3>Treatment of Customers</h3>
        <p>Customers are not respected or feel appreciated, no customer loyalty.</p>

        <h3>Treatment of Suppliers</h3>
        <p>Suppliers are put under undue pressure to perform, inconsistent supply chain, no supplier loyalty.</p>
    <?php $subsections[] = ['title' => 'Defining on organisation\'s weaknesses', 'type' => 'text', 'duration' => 120, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Defining on organisation\'s weaknesses' , 'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // The real consequences of loss ?>
    <?php ob_start(); ?>
        <p>The real value of assets is not measured solely on the financial value or market value at the time of loss. The true value of assets is measured in the context of the overall impact on the organisation. A battery stolen from a truck or forklift has a quantifiable value, the loss of the vehicle for hours awaiting replacement can be ten times the market value of the battery as staff are idle or customers do not receive deliveries.</p>

        <p>A courier services has a vehicle stolen, the market value is quantifiable, however; the vehicle may have a satellite navigation system installed with years of legacy information programmed in. There may be contacts, maps, routes and timing information on board. The loss of the vehicle and the loss of the time are serious but this is further compounded by the loss of ancillary and legacy assets, which can be irreplaceable.</p>

        <p>Consider the loss of a vehicle to a sole trader taxi driver. The vehicle by law has unique features such as signage, original identification information, a unique licensed meter. Full replacement can take weeks and income ability has ceased immediately.</p>

        <p>In looking at the broader dimensions of crime prevention, the true value of most assets to a thief can be substantiality less than the value to the organisation. An item of clothing or a piece of electrical equipment can be worth €200-00 in the organisations accounts but sold by a thief for half that and even less. The knock on effect on the organisation or individual who has lost the asset can in turn be ten times greater than the actual market value, when for example if it is raw material or machinery.</p>

        <p>A manufacturer may have a twenty-year-old machine still in use. The machine may be vandalised during an incident or destroyed in a fire. Insurance compensation will be paid based on the market value or replacement value, other protection may include compensation for loss of earnings however it can be very difficult to measure the long-term consequences of customers not getting deliveries on time.</p>
    <?php $subsections[] = ['title' => 'Real value of assets', 'type' => 'text', 'duration' => 120, 'content' => ob_get_clean()]; ?>

    <?php ob_start(); ?>
        <h3>Tangible consequences</h3>
        <p>Tangible consequences are the obvious noticeable consequences, the real and concrete effects that can be seen and felt. An amount of money is gone, a piece of equipment or property is damaged and the cost is quantifiable.</p>

        <h3>Intangible Consequences</h3>
        <p>Intangible consequences are the less obvious consequences, the impact may not be immediately apparent or easily quantifiable, production is stopped, and deliveries are delayed leading to the affect being transferred to a customer. The true loss and the longer-term loss are not immediately measurable and may never be known. Damage to an organisations reputation can be much more subtle, confidence in the organisation can be lost leading to a longer-term impact.</p>
    <?php $subsections[] = ['title' => 'Tangible and intangible consequences', 'type' => 'text', 'duration' => 40, 'content' => ob_get_clean()]; ?>


    <?php ob_start(); ?>
        <p>The previous sections mention the organisation and makes reference to the client customer, the consequences of loss can have a broader impact on staff and on the community, for example:</p>

        <h3>Staff</h3>
        <p>All losses have the potential to affect the organisations stability; this naturally can lead to job losses or less favourable terms and conditions of employment. Major incidents such as fire have led to the total collapse of organisations.</p>

        <h3>Community</h3>
        <p>There is a body of evidence that suggest the proceeds of certain crimes are used by criminals to invest in drugs or guns and expand their criminal operations. The consequences of crime lead to a much more serious impact on the community and indeed on society as a whole. In the case of major incidents such as fire or chemical leak, the consequences within the community can be factors where the community blames the organisation for causing harm, damage or loss to locals or their property.</p>

        <h3>Individuals</h3>
        <p>Finally, in certain circumstances, there are consequences arising from the actual event or incident that led to the loss. In cases where loss occurs through armed robbery, intimidation or tiger kidnapping, individuals such as staff members or customers are also victims. These incidents can lead to personal long-term suffering and additional costs on the organisation as the consequences can include bad publicity and compensation claims as well as affecting staff moral and customer loyalty.
    <?php $subsections[] = ['title' => 'Others affected', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'The real consequences of loss', 'type' => 'text', 'duration' => 120, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    ?>

<?php // Why investigate losses ?>
    <?php ob_start(); ?>
        <p>Losses are investigated to discover how they happened, who is responsible and to ascertain the full extent of the loss. An investigation will gather information on why or for what reasons particular losses or types of losses occur. Investigations ultimately provide information for the purpose of preventing or reducing the potential of re-occurrence.</p>

        <p>The results of investigations are reported to senior management to assist with decision making.</p>
    <?php $subsections[] = ['title' => 'Why investigate losses', 'type' => 'text', 'duration' => 60, 'content' => ob_get_clean()]; ?>

    <?php
    $sections[] = ['title' => 'Why investigate losses', 'type' => 'text', 'duration' => 60, 'complete' => 0, 'items' => $subsections];
    $subsections = []; // Reset
    unset($subsections);
    ?>
