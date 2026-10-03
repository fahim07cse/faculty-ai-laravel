@extends('layouts.app')
@section('title', 'Faculty AI Explorer')
@section('head')
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<style>
* {
box-sizing: border-box;
}
body {
margin: 0;
font-family: Arial, Helvetica, sans-serif;
background: #f5f7fb;
color: #1f2937;
}
.header {
background: #f3f6fa;
padding: 24px 34px 22px;
border: 1px solid #d7e0ea;
border-radius: 0 0 18px 18px;
}
.header h1 {
margin: 0;
font-size: 34px;
line-height: 1.15;
letter-spacing: -0.4px;
}
.header p {
margin: 7px 0 0;
color: #526174;
font-size: 17px;
line-height: 1.4;
}
.container {
max-width: 1520px;
margin: 18px auto 26px;
padding: 0 18px;
}
.stats {
display: grid;
grid-template-columns: repeat(3, 1fr);
gap: 14px;
margin-bottom: 20px;
}
.stat {
background: white;
border: 1px solid #d7e0ea;
border-radius: 13px;
padding: 14px 16px;
}
.stat-number {
font-size: 29px;
font-weight: 700;
line-height: 1;
}
.stat-label {
margin-top: 5px;
color: #64748b;
font-size: 14px;
}
.controls {
display: grid;
grid-template-columns: 2fr 1.05fr 1.15fr;
gap: 16px;
margin-bottom: 18px;
align-items: end;
}
.control-box label {
display: block;
font-weight: 700;
font-size: 15px;
margin-bottom: 6px;
}
select,
input {
width: 100%;
padding: 8px 10px;
border: 1px solid #aebdce;
border-radius: 7px;
background: white;
font-size: 14px;
min-height: 38px;
}
.main-grid {
display: grid;
grid-template-columns: minmax(0, 2.05fr) minmax(360px, 1fr);
gap: 18px;
align-items: start;
}
.card {
background: white;
border: 1px solid #d7e0ea;
border-radius: 13px;
overflow: hidden;
}
.card-title {
padding: 14px 18px;
font-size: 19px;
font-weight: 700;
background: #f8fafc;
border-bottom: 1px solid #e2e8f0;
}
#network {
width: 100%;
height: 700px;
background:
radial-gradient(circle at 50% 45%, #ffffff 0%, #fbfdff 58%, #f7faff 100%);
}
.legend {
padding: 10px 14px;
border-top: 1px solid #e2e8f0;
display: flex;
gap: 16px;
flex-wrap: wrap;
font-size: 14px;
}
.legend-item {
display: flex;
align-items: center;
gap: 6px;
}
.circle {
width: 13px;
height: 13px;
border-radius: 50%;
}
.blue {
background: #3478ed;
}
.purple {
background: #8b5cf6;
}
.green {
background: #218c83;
}
#profile {
padding: 20px 20px 18px;
min-height: 700px;
font-size: 15px;
line-height: 1.5;
overflow-wrap: break-word;
}
.profile-name {
font-size: 29px;
font-weight: 700;
line-height: 1.1;
margin-bottom: 4px;
}
.department {
color: #64748b;
font-size: 16px;
margin-bottom: 14px;
}
.section-title {
font-weight: 700;
margin-top: 20px;
margin-bottom: 8px;
color: #1f2937;
font-size: 16px;
line-height: 1.3;
}
.tags {
display: flex;
flex-wrap: wrap;
gap: 7px 8px;
}
.tag {
background: #eef2ff;
color: #2737a6;
padding: 6px 10px;
border-radius: 16px;
font-size: 13px;
line-height: 1.15;
}
.org-tag {
background: #e7f7f3;
color: #12635c;
}
.placeholder {
color: #94a3b8;
text-align: center;
padding-top: 100px;
}
.error {
color: #b91c1c;
padding: 20px;
}
.suggestions-title {
margin: 22px -20px 10px;
padding: 13px 20px;
background: #f8fafc;
border-top: 1px solid #e2e8f0;
border-bottom: 1px solid #e2e8f0;
font-size: 18px;
font-weight: 700;
}
.suggestion {
position: relative;
padding: 15px 45px 15px 12px;
border-bottom: 1px solid #e2e8f0;
border-radius: 10px;
cursor: pointer;
transition: background 0.15s ease, transform 0.15s ease;
}
.suggestion:hover {
background: #f8fafc;
transform: translateY(-1px);
}
.suggestion:last-child {
border-bottom: none;
}
.suggestion-name {
font-size: 18px;
font-weight: bold;
color: #1756a9;
}
.suggestion-department {
color: #64748b;
margin: 3px 0 8px;
}
.match-score {
position: absolute;
right: 0;
top: 15px;
min-width: 34px;
padding: 5px 8px;
text-align: center;
border-radius: 18px;
background: #eef4ff;
color: #1756a9;
font-weight: bold;
}
.match-text {
color: #475569;
line-height: 1.45;
padding-right: 40px;
}
.match-basis {
margin: 8px 0 14px;
padding: 10px 12px;
background: #f8fafc;
border: 1px solid #e2e8f0;
border-radius: 9px;
color: #475569;
font-size: 13px;
line-height: 1.45;
}
.profile-answer {
white-space: normal;
color: #1f2937;
font-size: 15px;
line-height: 1.6;
font-weight: 400;
overflow-wrap: break-word;
word-break: normal;
max-width: 100%;
margin: 0 0 8px;
}

.profile-answer .read-more-text {
  display: block;
  margin: 0;
}

.read-more-button {
  display: inline-block;
  margin-top: 6px;
  padding: 0;
  border: 0;
  background: transparent;
  color: #1756a9;
  font: inherit;
  font-weight: 700;
  cursor: pointer;
}
.read-more-button:hover {
  text-decoration: underline;
}
.read-more-extra {
  display: none;
}
.read-more-text.expanded .read-more-extra {
  display: inline;
}

.read-more-tag-extra {
  display: none;
}
.read-more-tags.expanded .read-more-tag-extra {
  display: inline-flex;
}
.tag-read-more-button {
  align-self: center;
  margin: 2px 0 2px 4px;
}
.match-breakdown {
display: flex;
flex-wrap: wrap;
gap: 6px;
margin-top: 8px;
}
.match-chip {
display: inline-flex;
align-items: center;
padding: 4px 8px;
border-radius: 999px;
background: #f1f5f9;
color: #475569;
font-size: 12px;
border: 1px solid #e2e8f0;
}
.match-reason {
margin-top: 7px;
color: #475569;
font-size: 13px;
line-height: 1.45;
}
.footer {
margin: 18px 0 30px;
color: #64748b;
font-size: 13px;
}
.top-actions {
display: flex;
justify-content: flex-start;
margin: 0 0 12px;
}
.add-button {
border: 0;
background: #1756a9;
color: white;
padding: 11px 16px;
border-radius: 9px;
font-size: 15px;
font-weight: bold;
cursor: pointer;
}
.add-button:hover {
filter: brightness(0.95);
}

.mode-chooser {
padding: 26px 30px 30px;
}
.mode-chooser p {
margin: 0 0 16px;
color: #64748b;
line-height: 1.5;
}
.mode-actions {
display: flex;
justify-content: flex-start;
gap: 10px;
flex-wrap: wrap;
}
.mode-button {
border: 0;
background: #1756a9;
color: white;
padding: 11px 16px;
border-radius: 9px;
font-size: 15px;
font-weight: bold;
cursor: pointer;
}
.mode-button.secondary {
background: #e8eef8;
color: #174a8b;
}
.edit-lookup {
padding: 26px 30px 30px;
}
.edit-lookup-row {
display: flex;
justify-content: flex-start;
gap: 10px;
align-items: flex-end;
flex-wrap: wrap;
}
.edit-lookup-row .field {
margin: 0;
min-width: min(420px, 100%);
flex: 0 1 420px;
}
.hidden {
display: none !important;
}
.form-mode-heading {
margin: 0 0 18px;
font-size: 18px;
color: #17365d;
}
.modal {
display: none;
position: fixed;
inset: 0;
background: rgba(15, 23, 42, 0.48);
z-index: 9999;
padding: 24px;
overflow-y: auto;
}
.modal.open {
display: block;
}
.modal-panel {
max-width: 900px;
margin: 20px auto;
background: white;
border-radius: 16px;
box-shadow: 0 20px 60px rgba(0,0,0,0.2);
overflow: hidden;
}
.modal-header {
display: flex;
justify-content: space-between;
align-items: center;
padding: 18px 22px;
border-bottom: 1px solid #e2e8f0;
background: #f8fafc;
}
.modal-header h2 {
margin: 0;
font-size: 22px;
}
.close-button {
border: 0;
background: transparent;
font-size: 28px;
cursor: pointer;
line-height: 1;
color: #475569;
}
.form-body {
padding: 22px;
}
.form-section {
margin-bottom: 26px;
}
.form-section h3 {
margin: 0 0 12px;
font-size: 18px;
}
.field {
margin-bottom: 16px;
}
.field label {
display: block;
font-weight: bold;
margin-bottom: 7px;
line-height: 1.35;
}
.field input[type="text"],
.field input[type="email"],
.field textarea,
.field select {
width: 100%;
padding: 11px;
border: 1px solid #cbd5e1;
border-radius: 8px;
font-size: 15px;
font-family: inherit;
background: white;
}
.field textarea {
min-height: 110px;
resize: vertical;
}
.field input:invalid:not(:placeholder-shown) {
border-color: #b91c1c;
background: #fff7f7;
}
.email-help {
margin-top: 6px;
color: #64748b;
font-size: 13px;
}
.radio-row {
display: flex;
flex-direction: column;
gap: 8px;
margin-top: 8px;
}
.radio-row label {
font-weight: normal;
display: flex;
gap: 8px;
align-items: flex-start;
}
.radio-row.inline-options {
flex-direction: row;
flex-wrap: wrap;
gap: 14px 28px;
align-items: center;
}
.radio-row.inline-options label {
display: inline-flex;
align-items: center;
gap: 7px;
margin: 0;
}
@media (max-width: 650px) {
.radio-row.inline-options {
flex-direction: column;
align-items: flex-start;
gap: 10px;
}
}
/* Fix radio-button layout: override the global input width rule */
.radio-row input[type="radio"] {
width: auto;
min-width: 18px;
height: 18px;
margin: 0;
flex: 0 0 auto;
}
.radio-row.inline-options {
display: flex;
flex-direction: row;
flex-wrap: wrap;
gap: 12px 28px;
align-items: flex-start;
}
.radio-row.inline-options label {
width: auto;
max-width: 100%;
display: inline-flex;
align-items: flex-start;
gap: 8px;
margin: 0;
line-height: 1.4;
white-space: normal;
}
/* Keep Q4 compact */
.radio-row.inline-options label:has(input[name="showcase_interest"]) {
flex: 0 0 auto;
}
/* Let long consent text wrap naturally instead of becoming a narrow column */
.radio-row.inline-options label:has(input[name="consent"]) {
flex: 1 1 320px;
min-width: 280px;
}
@media (max-width: 700px) {
.radio-row.inline-options {
flex-direction: column;
gap: 10px;
}
.radio-row.inline-options label:has(input[name="consent"]) {
min-width: 0;
width: 100%;
}
}
.form-actions {
display: flex;
justify-content: flex-end;
gap: 10px;
margin-top: 22px;
}
.secondary-button,
.submit-button {
padding: 10px 16px;
border-radius: 8px;
font-size: 15px;
cursor: pointer;
}
.secondary-button {
border: 1px solid #cbd5e1;
background: white;
}
.submit-button {
border: 0;
background: #1756a9;
color: white;
font-weight: bold;
}
.submit-button:disabled {
opacity: 0.6;
cursor: not-allowed;
}
.form-message {
margin-top: 14px;
font-size: 14px;
}
.form-message.success {
color: #166534;
}
.form-message.error {
color: #b91c1c;
}
@media (max-width: 850px) {
.stats,
.controls,
.main-grid {
grid-template-columns: 1fr;
}
#network {
height: 500px;
}
}
</style>
@endsection
@section('content')

<div class="header">
<h1>Faculty AI Explorer</h1>
<p>
Explore shared interests, complementary expertise and
external connections across Faculty AI activity.
</p>
</div>
<div class="container">
<div class="top-actions">
<button id="openFacultyForm" class="add-button" type="button">
Manage Faculty Data
</button>
</div>
<div class="stats">
<div class="stat">
<div id="staffCount" class="stat-number">0</div>
<div class="stat-label">colleagues</div>
</div>
<div class="stat">
<div id="themeCount" class="stat-number">0</div>
<div class="stat-label">research and practice themes</div>
</div>
<div class="stat">
<div id="orgCount" class="stat-number">0</div>
<div class="stat-label">external organisations or sectors</div>
</div>
</div>
<div class="controls">
<div class="control-box">
<label>Choose a colleague</label>
<select id="facultySelect">
<option value="">Select a colleague</option>
</select>
</div>
<div class="control-box">
<label>Connection view</label>
<select id="connectionView">
<option value="all">Themes + organisations</option>
<option value="selected">Selected colleague</option>
<option value="suggested">Suggested connections</option>
<option value="themes">Themes only</option>
<option value="organisations">Organisations only</option>
</select>
</div>
<div class="control-box">
<label>Filter network</label>
<input
id="searchBox"
type="text"
placeholder="e.g. ethics, VR, heritage"
>
</div>
</div>
<div class="main-grid">
<div class="card">
<div class="card-title">
Interactive network
</div>
<div id="network"></div>
<div class="legend">
<div class="legend-item">
<span class="circle blue"></span>
Staff
</div>
<div class="legend-item">
<span class="circle purple"></span>
Theme
</div>
<div class="legend-item">
<span class="circle green"></span>
Organisation/sector
</div>
</div>
</div>
<div class="card">
<div class="card-title">
Profile and suggested connections
</div>
<div id="profile">
<div class="placeholder">
Select a colleague to view their profile.
</div>
</div>
</div>
</div>
<div class="footer">
Connections are generated from self-reported responses
</div>
</div>
<div id="facultyModal" class="modal" aria-hidden="true">
<div class="modal-panel">
<div class="modal-header">
<h2>Manage Faculty Data</h2>
<button id="closeFacultyForm" class="close-button" type="button" aria-label="Close">&times;</button>
</div>
<div id="facultyModeChooser" class="mode-chooser">
  <p>Choose whether you want to add a new faculty record or edit an existing one.</p>
  <div class="mode-actions">
    <button id="chooseAddFaculty" class="mode-button" type="button">Add New Data</button>
    <button id="chooseEditFaculty" class="mode-button secondary" type="button">Edit Existing Data</button>
  </div>
</div>
<div id="editLookup" class="edit-lookup hidden">
  <h3 class="form-mode-heading">Edit Existing Data</h3>
  <div class="edit-lookup-row">
    <div class="field">
      <label for="editLookupEmail">University Email</label>
      <input id="editLookupEmail" type="email" placeholder="name@soton.ac.uk">
    </div>
    <button id="findFacultyData" class="mode-button" type="button">Find My Data</button>
    <button id="backToFacultyModes" class="secondary-button" type="button">Back</button>
  </div>
  <div id="editLookupMessage" class="form-message"></div>
</div>
<form id="facultyForm" class="form-body hidden">
<div id="formModeTitle" class="form-mode-heading"></div>
<div class="form-section">
<h3>About You</h3>
<div class="field">
<label for="formName">Name *</label>
<input id="formName" name="name" type="text" required>
</div>
<div class="field">
<label for="formEmail">Email</label>
<input
id="formEmail"
name="email"
type="email"
placeholder="name@soton.ac.uk"
pattern="^[A-Za-z0-9._%+-]+@soton\.ac\.uk$"
title="Please enter a valid University of Southampton email ending in @soton.ac.uk"
>
<div class="email-help">
Please use your University of Southampton email address ending in @soton.ac.uk.
For new records, each email address can be submitted only once. In edit mode, the email identifies the existing record.
</div>
</div>
<div class="field">
<label for="formDepartment">1. Department / Academic Unit *</label>
<input id="formDepartment" name="department" type="text" required>
</div>
<div class="field">
<label for="formResearchGroups">
2. Research Groups, Units or Project Affiliations (please include web links if available)
</label>
<textarea id="formResearchGroups" name="research_groups"></textarea>
</div>
<div class="field">
<label for="formKeywords">
3. Keywords: Please list up to five keywords that describe your AI-related work (separate words or phrases by comma) *
</label>
<textarea id="formKeywords" name="keywords" required></textarea>
</div>
<div class="field">
<label>4. Would you be interested to have your work showcased via Faculty comms and/or AI@Southampton?</label>
<div class="radio-row inline-options">
<label><input type="radio" name="showcase_interest" value="Yes, please contact me"> Yes, please contact me</label>
<label><input type="radio" name="showcase_interest" value="No"> No</label>
</div>
</div>
</div>
<div class="form-section">
<h3>Research, Scholarship, Enterprise</h3>
<div class="field">
<label for="formAiResearch">
5. Describe aspects of your research/scholarship as pertaining to AI (directly or indirectly). Include any key themes, methods, or case studies you are exploring.
</label>
<textarea id="formAiResearch" name="ai_research"></textarea>
</div>
<div class="field">
<label for="formEnterprise">
6. Describe enterprise, knowledge exchange, consultancy, or public-facing projects you are involved in relating to AI
</label>
<textarea id="formEnterprise" name="enterprise_projects"></textarea>
</div>
<div class="field">
<label for="formFuture">
7. What future directions or funding areas would you like to pursue?
</label>
<textarea id="formFuture" name="future_directions"></textarea>
</div>
<div class="field">
<label for="formExternal">
8. List external organisations, industries, or communities you are working with or would like to work with. Comment on how the Faculty might best support or coordinate these engagements
</label>
<textarea id="formExternal" name="external_organisations"></textarea>
</div>
<div class="field">
<label for="formSupport">
9. What kinds of support and/or training would help you develop your AI-related work? Include any particular ethical, legal, or technical issues you think the Faculty should address – where possible, provide suggestions for how the Faculty can develop its approach.
</label>
<textarea id="formSupport" name="support_training"></textarea>
</div>
<div class="field">
<label for="formComments">
10. Comment on anything else you’d like to share about your work with AI or your thoughts on Faculty-level coordination
</label>
<textarea id="formComments" name="additional_comments"></textarea>
</div>
</div>
<div class="form-section">
<h3>Consent</h3>
<div class="field">
<label>
11. I understand that my responses will be stored securely and used for internal research-mapping and strategic purposes within the Faculty. Any reporting will anonymise individual contributions unless I am contacted for permission. Where clear opportunities or shared interests emerge, colleagues may be contacted directly to help connect related work and foster collaboration.
</label>
<div class="radio-row inline-options">
<label>
<input type="checkbox" name="consent" value="I consent to my data being used for the stated purposes" required>
I consent to my data being used for the stated purposes
</label>
</div>
</div>
</div>
<div id="facultyFormMessage" class="form-message"></div>
<div class="form-actions">
<button id="cancelFacultyForm" class="secondary-button" type="button">Cancel</button>
<button id="facultySubmitButton" class="submit-button" type="submit">Submit</button>
</div>
</form>
</div>
</div>


@endsection
@section('scripts')
<script src="{{ asset('js/faculty.js') }}"></script>
@endsection
