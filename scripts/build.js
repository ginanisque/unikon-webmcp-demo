const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '..');
const output = path.join(root, 'dist-site');
fs.rmSync(output, {recursive: true, force: true});
fs.mkdirSync(output, {recursive: true});

for (const file of ['index.html', 'app.js', 'curriculum.js', 'state.js', 'webmcp.js', 'site.css', 'vercel.json']) {
  fs.copyFileSync(path.join(root, file), path.join(output, file));
}
const images = [
  'assets/fashion-elearning-unikon.webp',
  'public/images/courses/fashion-design-studio.webp',
  'public/images/courses/fashion-foundations-colour-wheel.webp',
  'public/images/courses/sewing-curved-lines.webp'
];
for (const image of images) {
  const destination = path.join(output, image);
  fs.mkdirSync(path.dirname(destination), {recursive: true});
  fs.copyFileSync(path.join(root, image), destination);
}
fs.mkdirSync(path.join(output, 'public', 'css'), {recursive: true});
fs.copyFileSync(path.join(root, 'public', 'css', 'learning-app.css'), path.join(output, 'public', 'css', 'learning-app.css'));
console.log(`Standalone site built in ${output}`);
