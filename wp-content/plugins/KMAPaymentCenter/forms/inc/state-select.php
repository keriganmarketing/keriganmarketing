<option value="">Please Select</option>
<optgroup label="Australian Provinces">
    <option value="-AU-NSW" <?php echo $billingState == "-AU-NSW" ? "selected" : "" ?>>New South Wales</option>
    <option value="-AU-QLD" <?php echo $billingState == "-AU-QLD" ? "selected" : "" ?>>Queensland</option>
    <option value="-AU-SA" <?php echo $billingState == "-AU-SA" ? "selected" : "" ?>>South Australia</option>
    <option value="-AU-TAS" <?php echo $billingState == "-AU-TAS" ? "selected" : "" ?>>Tasmania</option>
    <option value="-AU-VIC" <?php echo $billingState == "-AU-VIC" ? "selected" : "" ?>>Victoria</option>
    <option value="-AU-WA" <?php echo $billingState == "-AU-WA" ? "selected" : "" ?>>Western Australia</option>
    <option value="-AU-ACT" <?php echo $billingState == "-AU-ACT" ? "selected" : "" ?>>Australian Capital
        Territory
    </option>
    <option value="-AU-NT" <?php echo $billingState == "-AU-NT" ? "selected" : "" ?>>Northern Territory</option>
</optgroup>
<optgroup label="Canadian Provinces">
    <option value="AB" <?php echo $billingState == "AB" ? "selected" : "" ?>>Alberta</option>
    <option value="BC" <?php echo $billingState == "BC" ? "selected" : "" ?>>British Columbia</option>
    <option value="MB" <?php echo $billingState == "MB" ? "selected" : "" ?>>Manitoba</option>
    <option value="NB" <?php echo $billingState == "NB" ? "selected" : "" ?>>New Brunswick</option>
    <option value="NF" <?php echo $billingState == "NF" ? "selected" : "" ?>>Newfoundland</option>
    <option value="NT" <?php echo $billingState == "NT" ? "selected" : "" ?>>Northwest Territories</option>
    <option value="NS" <?php echo $billingState == "NS" ? "selected" : "" ?>>Nova Scotia</option>
    <option value="NVT" <?php echo $billingState == "NVT" ? "selected" : "" ?>>Nunavut</option>
    <option value="ON" <?php echo $billingState == "ON" ? "selected" : "" ?>>Ontario</option>
    <option value="PE" <?php echo $billingState == "PE" ? "selected" : "" ?>>Prince Edward Island</option>
    <option value="QC" <?php echo $billingState == "QC" ? "selected" : "" ?>>Quebec</option>
    <option value="SK" <?php echo $billingState == "SK" ? "selected" : "" ?>>Saskatchewan</option>
    <option value="YK" <?php echo $billingState == "YK" ? "selected" : "" ?>>Yukon</option>
</optgroup>
<optgroup label="US States">
    <option value="AL" <?php echo $billingState == "AL" ? "selected" : "" ?>>Alabama</option>
    <option value="AK" <?php echo $billingState == "AK" ? "selected" : "" ?>>Alaska</option>
    <option value="AZ" <?php echo $billingState == "AZ" ? "selected" : "" ?>>Arizona</option>
    <option value="AR" <?php echo $billingState == "AR" ? "selected" : "" ?>>Arkansas</option>
    <option value="BVI" <?php echo $billingState == "BVI" ? "selected" : "" ?>>British Virgin Islands</option>
    <option value="CA" <?php echo $billingState == "CA" ? "selected" : "" ?>>California</option>
    <option value="CO" <?php echo $billingState == "CO" ? "selected" : "" ?>>Colorado</option>
    <option value="CT" <?php echo $billingState == "CT" ? "selected" : "" ?>>Connecticut</option>
    <option value="DE" <?php echo $billingState == "DE" ? "selected" : "" ?>>Delaware</option>
    <option value="FL" <?php echo $billingState == "FL" || $billingState == '' ? "selected" : "" ?>>Florida</option>
    <option value="GA" <?php echo $billingState == "GA" ? "selected" : "" ?>>Georgia</option>
    <option value="GU" <?php echo $billingState == "GU" ? "selected" : "" ?>>Guam</option>
    <option value="HI" <?php echo $billingState == "HI" ? "selected" : "" ?>>Hawaii</option>
    <option value="ID" <?php echo $billingState == "ID" ? "selected" : "" ?>>Idaho</option>
    <option value="IL" <?php echo $billingState == "IL" ? "selected" : "" ?>>Illinois</option>
    <option value="IN" <?php echo $billingState == "IN" ? "selected" : "" ?>>Indiana</option>
    <option value="IA" <?php echo $billingState == "IA" ? "selected" : "" ?>>Iowa</option>
    <option value="KS" <?php echo $billingState == "KS" ? "selected" : "" ?>>Kansas</option>
    <option value="KY" <?php echo $billingState == "KY" ? "selected" : "" ?>>Kentucky</option>
    <option value="LA" <?php echo $billingState == "LA" ? "selected" : "" ?>>Louisiana</option>
    <option value="ME" <?php echo $billingState == "ME" ? "selected" : "" ?>>Maine</option>
    <option value="MP" <?php echo $billingState == "MP" ? "selected" : "" ?>>Mariana Islands</option>
    <option value="MPI" <?php echo $billingState == "MPI" ? "selected" : "" ?>>Mariana Islands (Pacific)</option>
    <option value="MD" <?php echo $billingState == "MD" ? "selected" : "" ?>>Maryland</option>
    <option value="MA" <?php echo $billingState == "MA" ? "selected" : "" ?>>Massachusetts</option>
    <option value="MI" <?php echo $billingState == "MI" ? "selected" : "" ?>>Michigan</option>
    <option value="MN" <?php echo $billingState == "MN" ? "selected" : "" ?>>Minnesota</option>
    <option value="MS" <?php echo $billingState == "MS" ? "selected" : "" ?>>Mississippi</option>
    <option value="MO" <?php echo $billingState == "MO" ? "selected" : "" ?>>Missouri</option>
    <option value="MT" <?php echo $billingState == "MT" ? "selected" : "" ?>>Montana</option>
    <option value="NE" <?php echo $billingState == "NE" ? "selected" : "" ?>>Nebraska</option>
    <option value="NV" <?php echo $billingState == "NV" ? "selected" : "" ?>>Nevada</option>
    <option value="NH" <?php echo $billingState == "NH" ? "selected" : "" ?>>New Hampshire</option>
    <option value="NJ" <?php echo $billingState == "NJ" ? "selected" : "" ?>>New Jersey</option>
    <option value="NM" <?php echo $billingState == "NM" ? "selected" : "" ?>>New Mexico</option>
    <option value="NY" <?php echo $billingState == "NY" ? "selected" : "" ?>>New York</option>
    <option value="NC" <?php echo $billingState == "NC" ? "selected" : "" ?>>North Carolina</option>
    <option value="ND" <?php echo $billingState == "ND" ? "selected" : "" ?>>North Dakota</option>
    <option value="OH" <?php echo $billingState == "OH" ? "selected" : "" ?>>Ohio</option>
    <option value="OK" <?php echo $billingState == "OK" ? "selected" : "" ?>>Oklahoma</option>
    <option value="OR" <?php echo $billingState == "OR" ? "selected" : "" ?>>Oregon</option>
    <option value="PA" <?php echo $billingState == "PA" ? "selected" : "" ?>>Pennsylvania</option>
    <option value="PR" <?php echo $billingState == "PR" ? "selected" : "" ?>>Puerto Rico</option>
    <option value="RI" <?php echo $billingState == "RI" ? "selected" : "" ?>>Rhode Island</option>
    <option value="SC" <?php echo $billingState == "SC" ? "selected" : "" ?>>South Carolina</option>
    <option value="SD" <?php echo $billingState == "SD" ? "selected" : "" ?>>South Dakota</option>
    <option value="TN" <?php echo $billingState == "TN" ? "selected" : "" ?>>Tennessee</option>
    <option value="TX" <?php echo $billingState == "TX" ? "selected" : "" ?>>Texas</option>
    <option value="UT" <?php echo $billingState == "UT" ? "selected" : "" ?>>Utah</option>
    <option value="VT" <?php echo $billingState == "VT" ? "selected" : "" ?>>Vermont</option>
    <option value="USVI" <?php echo $billingState == "USVI" ? "selected" : "" ?>>VI U.S. Virgin Islands</option>
    <option value="VA" <?php echo $billingState == "VA" ? "selected" : "" ?>>Virginia</option>
    <option value="WA" <?php echo $billingState == "WA" ? "selected" : "" ?>>Washington</option>
    <option value="DC" <?php echo $billingState == "DC" ? "selected" : "" ?>>Washington, D.C.</option>
    <option value="WV" <?php echo $billingState == "WV" ? "selected" : "" ?>>West Virginia</option>
    <option value="WI" <?php echo $billingState == "WI" ? "selected" : "" ?>>Wisconsin</option>
    <option value="WY" <?php echo $billingState == "WY" ? "selected" : "" ?>>Wyoming</option>
</optgroup>
<option value="N/A" <?php echo $billingState == "N/A" ? "selected" : "" ?>>Other</option>