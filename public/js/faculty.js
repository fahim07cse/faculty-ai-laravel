



let facultyData = [];
let network = null;
function nextFacultyId() {
const ids = facultyData
.map(person => Number(person.id))
.filter(Number.isFinite);
return ids.length ? Math.max(...ids) + 1 : 1;
}
let selectedPerson = null;
function splitItems(value) {
if (!value) {
return [];
}
return String(value)
.split(/[,;\n]/)
.map(item => item.trim())
.filter(Boolean);
}
function shortLabel(value, maxLength = 30) {
const text = String(value || "").trim();
if (text.length <= maxLength) return text;
return text.slice(0, maxLength - 1).trimEnd() + "…";
}
const THEME_TAXONOMY = [
["AI in education & assessment", ["assessment", "education", "teaching", "learning", "academic skills", "marking", "learner", "school"]],
["Language learning & translation", ["language", "linguistic", "translation", "phonological", "corpus", "writing assessment"]],
["Immersive media, VR & storytelling", ["vr", "immersive", "storytelling", "interactive fiction", "digital literature", "sound design", "ar studios"]],
["Policy & public engagement", ["policy", "public", "engagement", "community", "rights", "geopolitical"]],
["Arts & critical theory", ["art", "arts", "critical theory", "literature", "music", "creative labour", "media industries"]],
["Machine learning & data mining", ["machine learning", "data mining", "information extraction", "llm", "model"]],
["Cultural heritage & archives", ["heritage", "archive", "archival", "archaeolog", "museum", "holocaust", "shipwreck", "ancient pollen"]],
["People-centred & participatory AI", ["people-centred", "participatory", "co-creation", "human-ai collaboration", "community"]],
["Human autonomy & agency", ["autonomy", "agency", "decision-support", "influence", "manipulation", "metacognition"]],
["AI ethics", ["ethic", "responsible ai", "legal", "bias", "fairness", "rights"]],
["AI tools & workflows", ["copilot", "workflow", "ai-assisted", "ai use", "utility", "office-level", "tool"]],
["Accessibility & inclusive interaction", ["accessibility", "inclusive", "disability", "hearing-diverse", "intent recognition"]],
["Generative imaging & creative AI", ["generative", "creative ai", "ai-authorship", "sticky ai", "imaging"]],
["Algorithms & digital infrastructure", ["algorithm", "digital infrastructure", "automated", "data", "technical"]],
["Digital humanities", ["digital humanities", "digital literature", "historical", "history of llm", "archives"]],
["Creative co-creation", ["co-creation", "creative", "participatory", "collaborative"]],
["Future of work", ["future of work", "precarity", "labour", "workforce", "apprenticeship"]],
["Enterprise, consultancy & KE", ["enterprise", "consultancy", "knowledge exchange", "industry", "business"]],
["AI consciousness", ["consciousness", "emotion", "metacognition", "human autonomy"]],
["Children & digital rights", ["children", "child", "digital rights", "school competition"]]
];
const ORGANISATION_TAXONOMY = [
["Education sector", ["department for education", "school", "education", "teacher", "learner"]],
["Software & technology companies", ["software", "technology", "ai expertise", "technical ai", "copilot"]],
["Museums & heritage organisations", ["museum", "heritage organisation", "historic england", "tate britain", "kiran nadar"]],
["Universities & research institutes", ["university", "institute", "imperial college", "tu delft", "oxford", "southampton"]],
["Biennales & cultural foundations", ["biennale", "foundation", "gujral", "british council"]],
["Community & public groups", ["community", "young people", "charit", "public", "local communities"]],
["Archaeology & GLAM sector", ["archaeology", "archaeological", "glam"]],
["Accessibility & disability sector", ["accessibility", "disability", "hearing-diverse"]],
["VR/AR & immersive-media sector", ["vr/ar", "immersive media", "sound design", "inclusive design"]],
["Creative industries & media", ["creative industries", "media industries", "arts groups", "cinema", "creative work"]],
["Government & public bodies", ["commission", "government", "public sector", "department"]],
["Marine & Earth observation sector", ["marine", "oceanography", "copernicus", "emodnet", "hydrographic", "earth observation", "geological"]],
["International research networks", ["ehri-eric", "ehri-uk", "consortium", "network"]],
["Business & consultancy sector", ["business", "consultancy", "industry", "companies"]],
["Cultural & heritage charities", ["world jewish relief", "charity", "cultural heritage organisations"]],
["Research funding & partnership bodies", ["alan turing", "funding", "research institute", "special interest group"]]
];
function themeCorrelationText(person) {
return `
${person.keywords || ""}
${person.ai_research || ""}
${person.enterprise_projects || ""}
${person.future_directions || ""}
`.toLowerCase();
}
function organisationCorrelationText(person) {
return String(
person.external_organisations || ""
).toLowerCase();
}
function canonicalFromTaxonomy(text, taxonomy) {
return taxonomy
.filter(([label, keywords]) =>
keywords.some(keyword => text.includes(keyword))
)
.map(([label]) => label);
}
function canonicalThemes(person) {
return canonicalFromTaxonomy(
themeCorrelationText(person),
THEME_TAXONOMY
);
}
function canonicalOrganisations(person) {
return canonicalFromTaxonomy(
organisationCorrelationText(person),
ORGANISATION_TAXONOMY
);
}
function splitResearchGroups(value) {
if (!value) return [];
return String(value)
.split(/[;\n]/)
.map(item => item.trim())
.filter(Boolean);
}
function normaliseItems(value) {
return splitItems(value)
.map(item => item.toLowerCase());
}
function escapeHTML(value) {
if (value === null || value === undefined) {
return "";
}
return String(value)
.replaceAll("&", "&amp;")
.replaceAll("<", "&lt;")
.replaceAll(">", "&gt;")
.replaceAll('"', "&quot;")
.replaceAll("'", "&#039;");
}
const MATCH_FIELDS = [
{ key: "keywords", label: "Q3 Keywords", weight: 20, type: "keywords" },
{ key: "ai_research", label: "Q5 Research / scholarship", weight: 20, type: "text" },
{ key: "enterprise_projects", label: "Q6 Enterprise / KE", weight: 20, type: "text" },
{ key: "future_directions", label: "Q7 Future directions / funding", weight: 20, type: "text" },
{ key: "external_organisations", label: "Q8 External organisations / communities", weight: 20, type: "text" }
];
const MATCH_STOPWORDS = new Set([
"the", "and", "for", "with", "that", "this", "from", "into", "our", "their",
"your", "you", "are", "was", "were", "have", "has", "had", "will", "would",
"could", "should", "about", "across", "through", "using", "use", "used", "work",
"working", "research", "project", "projects", "faculty", "university", "related",
"relating", "artificial", "intelligence", "technology", "technologies", "area",
"areas", "interest", "interests", "include", "including", "current", "future",
"develop", "development", "support", "engagement", "engagements", "want", "like",
"also", "such", "more", "other", "some", "any", "not", "but", "can", "may",
"its", "they", "them", "than", "then", "when", "where", "which", "what", "who",
"how", "why", "been", "being", "between", "within", "without", "towards", "around",
"directly", "indirectly", "specific", "pertaining", "involved", "pursue", "ai"
]);
function normaliseConceptToken(token) {
let word = String(token || "")
.toLowerCase()
.replace(/^[-_]+|[-_]+$/g, "");
if (word.length > 5 && word.endsWith("ing")) word = word.slice(0, -3);
else if (word.length > 4 && word.endsWith("ed")) word = word.slice(0, -2);
else if (word.length > 4 && word.endsWith("ies")) word = word.slice(0, -3) + "y";
else if (word.length > 4 && word.endsWith("s") && !word.endsWith("ss")) word = word.slice(0, -1);
return word;
}
function conceptSet(value) {
const words = String(value || "")
.toLowerCase()
.replace(/https?:\/\/\S+/g, " ")
.replace(/[^a-z0-9+#-]+/g, " ")
.split(/\s+/)
.map(normaliseConceptToken)
.filter(word =>
word.length >= 3 &&
!MATCH_STOPWORDS.has(word) &&
!/^\d+$/.test(word)
);
return new Set(words);
}
function overlapCoefficient(setA, setB) {
if (!setA.size || !setB.size) return 0;
let overlap = 0;
const smaller = setA.size <= setB.size ? setA : setB;
const larger = setA.size <= setB.size ? setB : setA;
smaller.forEach(item => {
if (larger.has(item)) overlap += 1;
});
return overlap / Math.min(setA.size, setB.size);
}
function keywordSimilarity(valueA, valueB) {
const listA = splitItems(valueA)
.map(item => item.toLowerCase().trim())
.filter(Boolean);
const listB = splitItems(valueB)
.map(item => item.toLowerCase().trim())
.filter(Boolean);
if (!listA.length || !listB.length) return 0;
const setA = new Set(listA);
const setB = new Set(listB);
let exact = 0;
setA.forEach(item => {
if (setB.has(item)) exact += 1;
});
const exactScore = exact / Math.min(setA.size, setB.size);
const tokenScore = overlapCoefficient(
conceptSet(valueA),
conceptSet(valueB)
);
return Math.max(exactScore, tokenScore * 0.7);
}
function textSimilarity(valueA, valueB) {
return overlapCoefficient(
conceptSet(valueA),
conceptSet(valueB)
);
}
function sharedConcepts(valueA, valueB, limit = 6) {
const a = conceptSet(valueA);
const b = conceptSet(valueB);
return [...a]
.filter(item => b.has(item))
.slice(0, limit);
}
function calculateMatch(selected, candidate) {
const breakdown = [];
let weightedScore = 0;
MATCH_FIELDS.forEach(field => {
const a = selected[field.key] || "";
const b = candidate[field.key] || "";
const similarity = field.type === "keywords"
? keywordSimilarity(a, b)
: textSimilarity(a, b);
const points = similarity * field.weight;
weightedScore += points;
const concepts = sharedConcepts(a, b);
if (similarity > 0) {
breakdown.push({
key: field.key,
label: field.label,
similarity: similarity,
points: points,
concepts: concepts
});
}
});
return {
person: candidate,
score: Math.round(weightedScore),
breakdown: breakdown.sort((a, b) => b.points - a.points)
};
}
function getSuggestedConnections(selected) {
return facultyData
.filter(person =>
String(person.id) !== String(selected.id)
)
.map(person => calculateMatch(selected, person))
.filter(result => result.score > 0)
.sort((a, b) => b.score - a.score)
.slice(0, 5);
}
async function loadData() {
const { data, error } = await db
.from("ai_faculty_data")
.select("*")
.eq("is_deleted", false)
.order("name", { ascending: true });
if (error) {
console.error(error);
document.getElementById("profile").innerHTML =
`<div class="error">
Database error: ${escapeHTML(error.message)}
</div>`;
return;
}
facultyData = data || [];
updateStatistics();
populateDropdown(facultyData);
applyConnectionView();
}
function updateStatistics() {
const themes = new Set();
const organisations = new Set();
facultyData.forEach(person => {
canonicalThemes(person)
.forEach(item => themes.add(item));
canonicalOrganisations(person)
.forEach(item => organisations.add(item));
});
document.getElementById("staffCount").textContent =
facultyData.length;
document.getElementById("themeCount").textContent =
themes.size;
document.getElementById("orgCount").textContent =
organisations.size;
}
function populateDropdown(list) {
const select =
document.getElementById("facultySelect");
select.innerHTML =
`<option value="">
Select a colleague
</option>`;
list.forEach(person => {
const option =
document.createElement("option");
option.value = person.id;
option.textContent =
`${person.name || "Unnamed colleague"}${
person.department
? " — " + person.department
: ""
}`;
select.appendChild(option);
});
}
function cleanOrganisationItem(item) {
  let text = String(item || "").trim();

  // Remove common introductory / filler wording from Q8 before display.
  text = text
    .replace(/^(?:and\s+)?finally\s*[:,\-]?\s*/i, "")
    .replace(/^my\s+current\s+partners\s+are\s*:\s*/i, "")
    .replace(/^current\s+partners\s+are\s*:\s*/i, "")
    .replace(/^my\s+partners\s+are\s*:\s*/i, "")
    .trim();

  const fillerOnly = /^(?:my\s+current\s+partners\s+are|current\s+partners\s+are|my\s+partners\s+are|finally|and\s+finally)[:.\s-]*$/i;
  if (!text || fillerOnly.test(text)) return "";

  return text;
}

function makeTags(value, organisation = false, itemLimit = null) {
let items = splitItems(value);

if (organisation) {
  items = items
    .map(cleanOrganisationItem)
    .filter(Boolean);
}

if (!items.length) {
return "<span>Not provided</span>";
}

const hasLimit = Number.isFinite(itemLimit) && itemLimit > 0 && items.length > itemLimit;
const visibleItems = hasLimit ? items.slice(0, itemLimit) : items;
const hiddenItems = hasLimit ? items.slice(itemLimit) : [];

return `
<div class="tags${hasLimit ? " read-more-tags" : ""}">
${visibleItems.map(item => `
<span class="tag ${organisation ? "org-tag" : ""}">
${escapeHTML(item)}
</span>
`).join("")}
${hiddenItems.map(item => `
<span class="tag ${organisation ? "org-tag" : ""} read-more-tag-extra">
${escapeHTML(item)}
</span>
`).join("")}
${hasLimit ? `<button class="read-more-button tag-read-more-button" type="button" aria-expanded="false">Read more</button>` : ""}
</div>
`;
}
function makeResearchGroupTags(value) {
const items = splitResearchGroups(value);
if (!items.length) {
return "<span>Not provided</span>";
}
return `
<div class="tags">
${items.map(item => `
<span class="tag">
${escapeHTML(item)}
</span>
`).join("")}
</div>
`;
}
function limitedText(value, wordLimit = 200) {
  const text = String(value || "Not provided").trim() || "Not provided";
  const words = text.split(/\s+/);

  if (words.length <= wordLimit) {
    return `<span>${escapeHTML(text)}</span>`;
  }

  const visible = words.slice(0, wordLimit).join(" ");
  const hidden = words.slice(wordLimit).join(" ");

  return `
    <span class="read-more-text">
      <span>${escapeHTML(visible)}</span>
      <span class="read-more-extra"> ${escapeHTML(hidden)}</span>
      <button class="read-more-button" type="button" aria-expanded="false">Read more</button>
    </span>
  `;
}

function showProfile(person) {
const emailLink = person.email
? `<a href="mailto:${escapeHTML(person.email)}">Email colleague</a>`
: "";
const suggestions = getSuggestedConnections(person);
let suggestionsHTML = "";
if (!suggestions.length) {
suggestionsHTML = `
<div style="color:#64748b;">
No Q3/Q5/Q6/Q7/Q8-based connections found yet.
</div>
`;
} else {
suggestionsHTML = suggestions.map(result => {
const colleague = result.person;
return `
<div class="suggestion" data-id="${escapeHTML(colleague.id)}">
<div class="suggestion-name">
${escapeHTML(colleague.name || "Unnamed colleague")}
</div>
<div class="suggestion-department">
${escapeHTML(colleague.department || "Department not provided")}
</div>
</div>
`;
}).join("");
}
document.getElementById("profile").innerHTML = `
<div class="profile-name">
${escapeHTML(person.name || "Unnamed colleague")}
</div>
<div class="department">
${escapeHTML(person.department || "Department not provided")}
${emailLink ? " · " + emailLink : ""}
</div>
<div class="section-title">
Research groups / themes
</div>
${makeResearchGroupTags(person.research_groups)}
<div class="section-title">
Keywords / interests
</div>
${makeTags(person.keywords)}
<div class="section-title">
Research/activity
</div>
<div class="profile-answer">
${limitedText(person.ai_research, 50)}
</div>
<div class="section-title">
Future direction
</div>
<div class="profile-answer">
${limitedText(person.future_directions, 50)}
</div>
<div class="section-title">
External organisations / sectors
</div>
${makeTags(person.external_organisations, true, 6)}
<div class="suggestions-title">
Suggested connections
</div>
${suggestionsHTML}
`;
}
function openColleague(id) {
const person = facultyData.find(
item => String(item.id) === String(id)
);
if (!person) return;
const select = document.getElementById("facultySelect");
if (![...select.options].some(option => String(option.value) === String(person.id))) {
document.getElementById("searchBox").value = "";
populateDropdown(facultyData);
}
select.value = String(person.id);
selectedPerson = person;
showProfile(person);
applyConnectionView();
document.querySelector(".main-grid").scrollIntoView({
behavior: "smooth",
block: "start"
});
}
function graphNodeKey(prefix, value) {
return (
prefix +
String(value || "")
.toLowerCase()
.replace(/[^a-z0-9]+/g, "_")
.replace(/^_+|_+$/g, "")
);
}
function buildDegreeMap(edges) {
const degree = new Map();
edges.forEach(edge => {
degree.set(
edge.from,
(degree.get(edge.from) || 0) + 1
);
degree.set(
edge.to,
(degree.get(edge.to) || 0) + 1
);
});
return degree;
}
function applyProfessionalNodeSizing(nodes, edges) {
const degree = buildDegreeMap(edges);
return nodes.map(node => {
const connections =
degree.get(node.id) || 0;
let size = 10;
if (node.group === "staff") {
size =
14 +
Math.min(connections, 12) * 0.55;
if (
selectedPerson &&
node.personId &&
String(node.personId) ===
String(selectedPerson.id)
) {
size += 4;
}
size = Math.min(size, 22);
}
if (node.group === "theme") {
size =
8.5 +
Math.min(connections, 12) * 0.32;
size = Math.min(size, 13);
}
if (node.group === "organisation") {
size =
8 +
Math.min(connections, 12) * 0.30;
size = Math.min(size, 12.5);
}
return {
...node,
size: size
};
});
}
function makeGraphEdge(from, to, type = "theme") {
return {
from: from,
to: to,
relationType: type,
width: 0.9,
color: {
color:
type === "organisation"
? "#c3d7d5"
: "#ccd6e5",
highlight: "#6b7f9d",
hover: "#8597b2",
opacity: 0.78
},
dashes:
type === "organisation"
? [4, 5]
: false,
smooth: {
enabled: true,
type: "dynamic",
roundness: 0.18
}
};
}
function drawFullNetwork(list, mode = "all") {
const nodes = [];
const edges = [];
const nodeIds = new Set();
function addNode(node) {
if (!nodeIds.has(node.id)) {
nodeIds.add(node.id);
nodes.push(node);
}
}
list.forEach(person => {
const staffId =
`staff_${person.id}`;
addNode({
id: staffId,
label:
shortLabel(
person.name || "Unnamed",
25
),
group: "staff",
title:
person.name || "Unnamed",
shape: "dot",
personId:
String(person.id)
});
if (
mode !== "organisations"
) {
canonicalThemes(person)
.forEach(theme => {
const themeId =
graphNodeKey(
"theme_",
theme
);
addNode({
id: themeId,
label:
shortLabel(
theme,
30
),
group: "theme",
title: theme,
shape: "dot"
});
edges.push(
makeGraphEdge(
staffId,
themeId,
"theme"
)
);
});
}
if (
mode !== "themes"
) {
canonicalOrganisations(person)
.forEach(org => {
const orgId =
graphNodeKey(
"org_",
org
);
addNode({
id: orgId,
label:
shortLabel(
org,
30
),
group: "organisation",
title: org,
shape: "dot"
});
edges.push(
makeGraphEdge(
staffId,
orgId,
"organisation"
)
);
});
}
});
renderNetwork(
applyProfessionalNodeSizing(
nodes,
edges
),
edges
);
}
function drawSuggestedNetwork(person) {
const suggestions = getSuggestedConnections(person);
const nodes = [];
const edges = [];
const selectedId = `suggest_staff_${person.id}`;
nodes.push({
id: selectedId,
label: shortLabel(person.name || "Unnamed", 25),
group: "staff",
title: person.name || "Unnamed",
shape: "dot",
personId: String(person.id),
size: 26
});
suggestions.forEach(result => {
const colleague = result.person;
const colleagueId = `suggest_staff_${colleague.id}`;
const reasons = result.breakdown
.slice(0, 3)
.map(item => item.label)
.join(", ");
nodes.push({
id: colleagueId,
label: shortLabel(colleague.name || "Unnamed", 25),
group: "staff",
title: `${colleague.name || "Unnamed"}${reasons ? " · Suggested based on " + reasons : ""}`,
shape: "dot",
personId: String(colleague.id),
size: 15 + Math.min(result.score, 100) * 0.07
});
edges.push({
from: selectedId,
to: colleagueId,
relationType: "match",
width: 1.2 + Math.min(result.score, 100) * 0.035,
label: "",
title: reasons ? `Suggested based on ${reasons}` : "Suggested connection",
color: {
color: "#9fb2cf",
highlight: "#4f6f9f",
hover: "#6d86aa",
opacity: 0.9
},
font: {
size: 11,
align: "middle",
background: "#ffffff",
color: "#475569"
},
smooth: {
enabled: true,
type: "dynamic",
roundness: 0.18
}
});
});
renderNetwork(nodes, edges);
}
function getFilteredFaculty() {
const query =
document.getElementById("searchBox")
.value
.trim()
.toLowerCase();
if (!query) {
return facultyData;
}
return facultyData.filter(person => {
const text = `
${person.name || ""}
${person.department || ""}
${person.keywords || ""}
${person.ai_research || ""}
${person.enterprise_projects || ""}
${person.future_directions || ""}
${person.external_organisations || ""}
`.toLowerCase();
return text.includes(query);
});
}
function applyConnectionView() {
const mode =
document.getElementById("connectionView").value;
if (mode === "selected") {
if (selectedPerson) {
drawPersonNetwork(
selectedPerson
);
} else {
drawFullNetwork(
getFilteredFaculty()
);
}
return;
}
if (mode === "suggested") {
if (selectedPerson) {
drawSuggestedNetwork(
selectedPerson
);
} else {
drawFullNetwork(
getFilteredFaculty()
);
}
return;
}
if (mode === "themes") {
drawFullNetwork(
getFilteredFaculty(),
"themes"
);
return;
}
if (
mode === "organisations"
) {
drawFullNetwork(
getFilteredFaculty(),
"organisations"
);
return;
}
drawFullNetwork(
getFilteredFaculty(),
"all"
);
}
function drawPersonNetwork(person) {
const nodes = [];
const edges = [];
const staffId = `selected_${person.id}`;
nodes.push({
id: staffId,
label: person.name || "Staff",
group: "staff",
title: person.name || "Staff",
shape: "dot",
personId: String(person.id)
});
canonicalThemes(person).forEach((theme, index) => {
const id = `selected_theme_${index}`;
nodes.push({
id: id,
label: shortLabel(theme, 30),
group: "theme",
title: `${theme} · derived from Q3/Q5/Q6/Q7`,
shape: "dot"
});
edges.push(makeGraphEdge(staffId, id, "theme"));
});
canonicalOrganisations(person).forEach((org, index) => {
const id = `selected_org_${index}`;
nodes.push({
id: id,
label: shortLabel(org, 30),
group: "organisation",
title: `${org} · derived from Q8`,
shape: "dot"
});
edges.push(makeGraphEdge(staffId, id, "organisation"));
});
const sizedNodes = applyProfessionalNodeSizing(nodes, edges).map(node => {
if (node.id === staffId) return { ...node, size: 24 };
return node;
});
renderNetwork(sizedNodes, edges);
}
function renderNetwork(nodes, edges) {
const container =
document.getElementById(
"network"
);
if (network) {
try {
network.destroy();
} catch (error) {
console.warn(error);
}
network = null;
}
const graphData = {
nodes:
new vis.DataSet(
nodes
),
edges:
new vis.DataSet(
edges
)
};
const options = {
autoResize: true,
groups: {
staff: {
color: {
background:
"#3b82f6",
border:
"#2563eb",
highlight: {
background:
"#2563eb",
border:
"#1d4ed8"
},
hover: {
background:
"#4f8ff7",
border:
"#2563eb"
}
},
font: {
size: 14,
color:
"#1f2937",
face:
"Arial",
strokeWidth: 3,
strokeColor:
"#ffffff"
}
},
theme: {
color: {
background:
"#8b5cf6",
border:
"#7c3aed",
highlight: {
background:
"#7c3aed",
border:
"#6d28d9"
}
},
font: {
size: 12,
color:
"#334155",
face:
"Arial",
strokeWidth: 3,
strokeColor:
"#ffffff"
}
},
organisation: {
color: {
background:
"#2a9188",
border:
"#187b74",
highlight: {
background:
"#187b74",
border:
"#0f766e"
}
},
font: {
size: 12,
color:
"#334155",
face:
"Arial",
strokeWidth: 3,
strokeColor:
"#ffffff"
}
}
},
nodes: {
borderWidth: 1.5,
borderWidthSelected: 2.5,
shadow: {
enabled: true,
color:
"rgba(15,23,42,0.10)",
size: 8,
x: 0,
y: 2
},
chosen: true
},
edges: {
selectionWidth: 2,
hoverWidth: 1.4,
smooth: {
enabled: true,
type:
"dynamic",
roundness: 0.18
}
},
physics: {
enabled: true,
solver:
"barnesHut",
stabilization: {
enabled: true,
iterations: 320,
updateInterval: 30,
fit: true
},
barnesHut: {
gravitationalConstant:
-7600,
centralGravity:
0.12,
springLength:
175,
springConstant:
0.027,
damping:
0.26,
avoidOverlap:
0.9
},
minVelocity:
0.75
},
interaction: {
hover: true,
hoverConnectedEdges:
true,
selectConnectedEdges:
true,
navigationButtons:
true,
keyboard: true,
zoomView: true,
dragView: true,
dragNodes: true,
tooltipDelay: 100,
hideEdgesOnDrag:
true
}
};
network =
new vis.Network(
container,
graphData,
options
);
network.once(
"stabilizationIterationsDone",
function() {
network.setOptions({
physics: false
});
network.fit({
animation: {
duration: 420,
easingFunction:
"easeInOutQuad"
}
});
setTimeout(
function() {
const currentScale =
network.getScale();
if (
currentScale < 0.72
) {
network.moveTo({
scale: 0.72,
animation: {
duration: 250,
easingFunction:
"easeInOutQuad"
}
});
}
},
460
);
}
);
network.on(
"hoverNode",
function(params) {
const connected =
network.getConnectedEdges(
params.node
);
graphData.edges.update(
connected.map(edgeId => {
const edge =
graphData.edges.get(
edgeId
);
return {
id: edgeId,
width: 2,
color: {
color:
"#8799b5",
opacity: 0.95
}
};
})
);
}
);
network.on(
"blurNode",
function() {
graphData.edges.forEach(
function(edge) {
const isOrg =
edge.relationType ===
"organisation";
graphData.edges.update({
id: edge.id,
width: 0.9,
color: {
color:
isOrg
? "#c3d7d5"
: "#ccd6e5",
highlight:
"#6b7f9d",
hover:
"#8597b2",
opacity:
0.78
}
});
}
);
}
);
network.on(
"click",
function(params) {
if (
!params.nodes ||
!params.nodes.length
) {
return;
}
const node =
graphData.nodes.get(
params.nodes[0]
);
if (
node &&
node.personId
) {
openColleague(
node.personId
);
}
}
);
}
document
.getElementById("facultySelect")
.addEventListener("change", function() {
if (!this.value) {
selectedPerson = null;
document.getElementById("profile").innerHTML = `
<div class="placeholder">
Select a colleague to view their profile.
</div>
`;
applyConnectionView();
return;
}
const person =
facultyData.find(
item =>
String(item.id) ===
String(this.value)
);
if (!person) {
return;
}
selectedPerson = person;
showProfile(person);
applyConnectionView();
});
document
.getElementById("searchBox")
.addEventListener("input", function() {
const filtered = getFilteredFaculty();
populateDropdown(filtered);
if (
selectedPerson &&
filtered.some(
person =>
String(person.id) ===
String(selectedPerson.id)
)
) {
document.getElementById("facultySelect").value =
String(selectedPerson.id);
} else if (this.value.trim()) {
document.getElementById("facultySelect").value = "";
}
applyConnectionView();
});
document
.getElementById("connectionView")
.addEventListener("change", function() {
applyConnectionView();
});
document.getElementById("profile").addEventListener("click", function(event) {
  const readMoreButton = event.target.closest(".read-more-button");
  if (readMoreButton) {
    event.preventDefault();
    event.stopPropagation();

    const textWrapper = readMoreButton.closest(".read-more-text");
    const tagsWrapper = readMoreButton.closest(".read-more-tags");
    const wrapper = textWrapper || tagsWrapper;
    if (!wrapper) return;

    const expanded = wrapper.classList.toggle("expanded");
    readMoreButton.textContent = expanded ? "Read less" : "Read more";
    readMoreButton.setAttribute("aria-expanded", expanded ? "true" : "false");
    return;
  }

  const card = event.target.closest(".suggestion[data-id]");
  if (!card) return;
  openColleague(card.dataset.id);
});
const facultyModal = document.getElementById("facultyModal");
const facultyForm = document.getElementById("facultyForm");
const facultyFormMessage = document.getElementById("facultyFormMessage");
const facultyModeChooser = document.getElementById("facultyModeChooser");
const editLookup = document.getElementById("editLookup");
const editLookupMessage = document.getElementById("editLookupMessage");
const facultySubmitButton = document.getElementById("facultySubmitButton");

let facultyFormMode = null;
let editingRecordId = null;
let editingOriginalEmail = "";

function setFacultyManagerScreen(screen) {
  facultyModeChooser.classList.toggle("hidden", screen !== "chooser");
  editLookup.classList.toggle("hidden", screen !== "lookup");
  facultyForm.classList.toggle("hidden", screen !== "form");
}

function resetFacultyFormState() {
  facultyForm.reset();
  facultyFormMode = null;
  editingRecordId = null;
  editingOriginalEmail = "";
  document.getElementById("formEmail").readOnly = false;
  document.getElementById("editLookupEmail").value = "";
  document.getElementById("formModeTitle").textContent = "";
  facultySubmitButton.textContent = "Submit";
  facultyFormMessage.textContent = "";
  facultyFormMessage.className = "form-message";
  editLookupMessage.textContent = "";
  editLookupMessage.className = "form-message";
}

function openFacultyModal() {
  resetFacultyFormState();
  setFacultyManagerScreen("chooser");
  facultyModal.classList.add("open");
  facultyModal.setAttribute("aria-hidden", "false");
}

function closeFacultyModal() {
  facultyModal.classList.remove("open");
  facultyModal.setAttribute("aria-hidden", "true");
}

function showAddFacultyForm() {
  facultyForm.reset();
  facultyFormMode = "add";
  editingRecordId = null;
  editingOriginalEmail = "";
  document.getElementById("formEmail").readOnly = false;
  document.getElementById("formModeTitle").textContent = "Add New Data";
  facultySubmitButton.textContent = "Submit";
  facultyFormMessage.textContent = "";
  facultyFormMessage.className = "form-message";
  setFacultyManagerScreen("form");
}

function showEditLookup() {
  facultyForm.reset();
  facultyFormMode = "edit";
  editingRecordId = null;
  editingOriginalEmail = "";
  editLookupMessage.textContent = "";
  editLookupMessage.className = "form-message";
  setFacultyManagerScreen("lookup");
  document.getElementById("editLookupEmail").focus();
}

function setRadioValue(name, value) {
  document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
    input.checked = String(input.value) === String(value || "");
  });
}

function populateFacultyForm(person) {
  const values = {
    formName: person.name,
    formEmail: person.email,
    formDepartment: person.department,
    formResearchGroups: person.research_groups,
    formKeywords: person.keywords,
    formAiResearch: person.ai_research,
    formEnterprise: person.enterprise_projects,
    formFuture: person.future_directions,
    formExternal: person.external_organisations,
    formSupport: person.support_training,
    formComments: person.additional_comments
  };

  Object.entries(values).forEach(([id, value]) => {
    const element = document.getElementById(id);
    if (element) element.value = value || "";
  });

  setRadioValue("showcase_interest", person.showcase_interest);

  // Q11 must be actively ticked for every Add or Edit save.
  const consentInput = facultyForm.querySelector('input[name="consent"]');
  if (consentInput) consentInput.checked = false;
}

async function findFacultyForEdit() {
  const email = String(document.getElementById("editLookupEmail").value || "").trim();
  const sotonEmailPattern = /^[A-Za-z0-9._%+-]+@soton\.ac\.uk$/i;

  if (!email || !sotonEmailPattern.test(email)) {
    editLookupMessage.textContent = "Please enter a valid University of Southampton email ending in @soton.ac.uk.";
    editLookupMessage.className = "form-message error";
    return;
  }

  editLookupMessage.textContent = "Loading your data...";
  editLookupMessage.className = "form-message";

  try {
    const { data, error } = await db
      .from("ai_faculty_data")
      .select("*")
      .ilike("email", email)
      .limit(1);

    if (error) throw error;

    if (!data || !data.length) {
      editLookupMessage.textContent = "No faculty record was found for this email address.";
      editLookupMessage.className = "form-message error";
      return;
    }

    const person = data[0];
    facultyFormMode = "edit";
    editingRecordId = person.id;
    editingOriginalEmail = String(person.email || "").trim();
    populateFacultyForm(person);
    document.getElementById("formEmail").readOnly = true;
    document.getElementById("formModeTitle").textContent = "Edit Existing Data";
    facultySubmitButton.textContent = "Save Changes";
    facultyFormMessage.textContent = "";
    facultyFormMessage.className = "form-message";
    setFacultyManagerScreen("form");
  } catch (error) {
    console.error(error);
    editLookupMessage.textContent = "Could not load the record: " + (error.message || "Unknown error");
    editLookupMessage.className = "form-message error";
  }
}

document.getElementById("openFacultyForm").addEventListener("click", openFacultyModal);
document.getElementById("closeFacultyForm").addEventListener("click", closeFacultyModal);
document.getElementById("cancelFacultyForm").addEventListener("click", closeFacultyModal);
document.getElementById("chooseAddFaculty").addEventListener("click", showAddFacultyForm);
document.getElementById("chooseEditFaculty").addEventListener("click", showEditLookup);
document.getElementById("backToFacultyModes").addEventListener("click", function() {
  resetFacultyFormState();
  setFacultyManagerScreen("chooser");
});
document.getElementById("findFacultyData").addEventListener("click", findFacultyForEdit);
document.getElementById("editLookupEmail").addEventListener("keydown", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    findFacultyForEdit();
  }
});

facultyModal.addEventListener("click", function(event) {
  if (event.target === facultyModal) closeFacultyModal();
});

facultyForm.addEventListener("submit", async function(event) {
  event.preventDefault();

  const submitButton = facultySubmitButton;
  const formData = new FormData(facultyForm);
  const consent = formData.get("consent");
  const email = String(formData.get("email") || "").trim();
  const sotonEmailPattern = /^[A-Za-z0-9._%+-]+@soton\.ac\.uk$/i;

  if (!email || !sotonEmailPattern.test(email)) {
    facultyFormMessage.textContent = "Invalid email. Please enter a valid University of Southampton email ending in @soton.ac.uk.";
    facultyFormMessage.className = "form-message error";
    document.getElementById("formEmail").focus();
    return;
  }

  if (consent !== "I consent to my data being used for the stated purposes") {
    facultyFormMessage.textContent = facultyFormMode === "edit"
      ? "Changes were not saved because consent was not provided."
      : "Submission was not sent because consent was not provided.";
    facultyFormMessage.className = "form-message error";
    return;
  }

  if (facultyFormMode === "add") {
    const { data: existingEmailRows, error: existingEmailError } = await db
      .from("ai_faculty_data")
      .select("id,email")
      .ilike("email", email);

    if (existingEmailError) {
      console.error(existingEmailError);
      facultyFormMessage.textContent = "Could not validate the email address. Please try again.";
      facultyFormMessage.className = "form-message error";
      return;
    }

    if (existingEmailRows && existingEmailRows.length > 0) {
      facultyFormMessage.textContent = "This University of Southampton email address has already been submitted. Please use Edit Existing Data instead.";
      facultyFormMessage.className = "form-message error";
      document.getElementById("formEmail").focus();
      return;
    }
  }

  const keywordText = String(formData.get("keywords") || "");
  const keywords = keywordText.split(",").map(item => item.trim()).filter(Boolean);

  if (keywords.length > 5) {
    facultyFormMessage.textContent = "Please provide no more than five keywords.";
    facultyFormMessage.className = "form-message error";
    return;
  }

  const now = new Date().toISOString();
  const payload = {
    email: email,
    name: formData.get("name") || null,
    last_modified_time: now,
    department: formData.get("department") || null,
    research_groups: formData.get("research_groups") || null,
    keywords: keywords.join(", "),
    showcase_interest: formData.get("showcase_interest") || null,
    ai_research: formData.get("ai_research") || null,
    enterprise_projects: formData.get("enterprise_projects") || null,
    future_directions: formData.get("future_directions") || null,
    external_organisations: formData.get("external_organisations") || null,
    support_training: formData.get("support_training") || null,
    additional_comments: formData.get("additional_comments") || null,
    consent: consent
  };

  if (facultyFormMode === "add") {
    payload.start_time = now;
    payload.completion_time = now;
  }

  submitButton.disabled = true;
  submitButton.textContent = facultyFormMode === "edit" ? "Saving..." : "Submitting...";
  facultyFormMessage.textContent = facultyFormMode === "edit" ? "Saving changes..." : "Submitting...";
  facultyFormMessage.className = "form-message";

  try {
    let result;

    if (facultyFormMode === "edit") {
      if (editingRecordId === null || editingRecordId === undefined) {
        throw new Error("No record is selected for editing.");
      }

      result = await db
        .from("ai_faculty_data")
        .update(payload)
        .eq("id", editingRecordId)
        .select();
    } else {
      result = await db
        .from("ai_faculty_data")
        .insert(payload)
        .select();
    }

    if (result.error) throw result.error;

    const saved = Array.isArray(result.data) && result.data.length ? result.data[0] : null;

    facultyFormMessage.textContent = facultyFormMode === "edit"
      ? "Changes saved successfully."
      : "Submitted successfully.";
    facultyFormMessage.className = "form-message success";

    await loadData();

    const savedId = saved && saved.id !== undefined ? saved.id : editingRecordId;
    setTimeout(() => {
      closeFacultyModal();
      if (savedId !== null && savedId !== undefined) openColleague(savedId);
    }, 600);

  } catch (error) {
    console.error(error);
    facultyFormMessage.textContent = (facultyFormMode === "edit" ? "Could not save changes: " : "Could not submit data: ") + (error.message || "Unknown error");
    facultyFormMessage.className = "form-message error";
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = facultyFormMode === "edit" ? "Save Changes" : "Submit";
  }
});

loadData();
