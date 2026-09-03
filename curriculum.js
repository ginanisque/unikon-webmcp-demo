(function () {
  'use strict';

  const choice = (id, title, prompt, choices, correct, keywords, min = 12, max = 280) =>
    ({ id, title, prompt, type: 'choice', choices, correct, keywords, minLength: min, maxLength: max });
  const written = (id, title, prompt, keywords, min = 40, max = 420, type = 'short_answer') =>
    ({ id, title, prompt, type, choices: {}, correct: null, keywords, minLength: min, maxLength: max });

  const sewingTopics = [
    ['threading-machine', 'Threading the Sewing Machine', ['thread', 'guide', 'tension', 'needle', 'presser']],
    ['machine-tension', 'Machine Tension', ['tension', 'balanced', 'stitch', 'upper', 'bobbin']],
    ['guide-fabric', 'Guiding the Fabric', ['guide', 'fabric', 'feed', 'hands', 'control']],
    ['practice-preparation', 'Prepare for Practice', ['practice', 'needle', 'machine', 'safety', 'setup']],
    ['straight-lines', 'Straight Lines', ['straight', 'seam', 'guide', 'allowance', 'speed']],
    ['curved-lines', 'Curved Lines', ['curve', 'pivot', 'guide', 'slow', 'needle']],
    ['angled-lines', 'Angled Lines', ['angle', 'corner', 'pivot', 'needle', 'turn']],
    ['science-repetition', 'Skill Building Through Repetition', ['repeat', 'practice', 'control', 'accuracy', 'consistent']],
    ['basic-zipper', 'Basic Zipper', ['zipper', 'foot', 'teeth', 'baste', 'seam']],
    ['invisible-zipper', 'Invisible Zipper', ['invisible', 'coil', 'foot', 'press', 'teeth']],
    ['decorative-zipper', 'Decorative Zipper', ['decorative', 'zipper', 'topstitch', 'placement', 'visible']],
    ['front-fly-zipper', 'Front-Fly Zipper', ['fly', 'zipper', 'shield', 'extension', 'topstitch']],
    ['topstitch-understitch', 'Topstitching and Understitching', ['topstitch', 'understitch', 'edge', 'facing', 'seam']],
    ['reinforce-stitching', 'Reinforcement Stitching', ['reinforce', 'backstitch', 'stress', 'secure', 'strength']],
    ['ease-gathers', 'Ease and Gathers', ['ease', 'gather', 'stitch', 'distribute', 'fullness']],
    ['sew-darts', 'Sewing Darts', ['dart', 'point', 'taper', 'press', 'shape']],
    ['sew-corners', 'Sewing Corners', ['corner', 'pivot', 'trim', 'turn', 'needle']],
    ['plackets', 'Plackets', ['placket', 'opening', 'reinforce', 'fold', 'closure']],
    ['blind-hemming', 'Blind Hemming Reflection', ['blind', 'hem', 'fold', 'stitch', 'finish']]
  ].map((topic, index, all) => ({...written(
    topic[0], `Practice ${index + 1}: ${topic[1]}`,
    index === all.length - 1
      ? 'Write a final reflection connecting blind hemming to at least two earlier control or finishing techniques in this course.'
      : 'Describe the key control point for this technique and one detail you would check during practice.',
    topic[2], index === all.length - 1 ? 140 : 40, index === all.length - 1 ? 1200 : 500,
    index === all.length - 1 ? 'essay' : 'short_answer'
  ), content: [
    'Follow the machine threading path in order: spool, guides, tension discs, take-up lever, final guides, then needle. Raise the presser foot while threading so the tension discs open.',
    'Balanced tension locks the upper and bobbin threads inside the fabric layers. Test on a matching scrap and change one setting at a time instead of pulling the fabric.',
    'Guide fabric with relaxed hands placed safely in front of the needle. Let the feed dogs move the cloth; pulling or pushing can distort stitches and bend the needle.',
    'Prepare with the power off near the needle, fit the correct needle and presser foot, wind and insert the bobbin, then test the setup on scrap fabric.',
    'For straight stitching, align the fabric edge with a seam guide, look ahead of the needle, and maintain an even speed. Backstitch briefly at each end when appropriate.',
    'Sew curves slowly with a shorter stitch if needed. Guide the fabric gradually without forcing it; stop with the needle down to make small adjustments.',
    'At an angle, stop exactly at the turning point with the needle down, lift the presser foot, pivot the fabric, lower the foot, and continue on the new line.',
    'Accuracy develops through short, focused repetition. Repeat the same sample, compare the results, identify one variable, and practise until control becomes consistent.',
    'A basic centred zipper is basted into a closed seam and stitched with a zipper foot at an even distance from the teeth. Remove basting only after checking alignment.',
    'For an invisible zipper, press the coils open gently and stitch close to them with the correct foot. Match the top edges before completing the seam below.',
    'A decorative zipper is intentionally visible, so placement and topstitching must be symmetrical. Mark the opening and secure both sides before stitching.',
    'A front-fly zipper uses an extension and shield to conceal and protect the closure. Keep the zipper clear while shaping the curved topstitching line.',
    'Topstitching is visible and controls an edge; understitching secures the seam allowance to a facing so it rolls inside. Press before either operation.',
    'Reinforce openings and stress points with backstitching, bar tacks, or a second stitching line. Keep reinforcement compact so it adds strength without bulk.',
    'Ease removes slight fullness smoothly; gathers create deliberate fullness. Use long gathering stitches, pull bobbin threads, and distribute fullness evenly before sewing.',
    'Mark both dart legs and the point accurately. Sew from the wide end toward the point, taper smoothly, secure without a bulky backstitch, then press in the intended direction.',
    'For crisp corners, stitch to the exact point, trim and grade excess seam allowance, turn carefully, shape the point without piercing it, and press.',
    'A placket finishes and strengthens an opening. Accurate marking, controlled clipping, even folding, and secure stitching prevent puckering at the base.',
    'A blind hem hides most stitching on the outside. Prepare an even hem, fold it to expose a narrow edge, catch only a few garment threads, then press without flattening the fold.'
  ][index]}));

  window.UnikonCurriculum = {
    'fashion-foundations': {
      id: 'fashion-foundations', title: 'Fashion Foundations: Fabric to Silhouette',
      description: 'Build a complete beginner garment plan by connecting fabric behaviour, grain, drape, construction, and silhouette.',
      hero: 'public/images/courses/fashion-foundations-colour-wheel.webp',
      lesson: { title: 'Choosing Fabric for a First A-Line Skirt', objective: 'Identify how weight, drape, and stability influence a clear A-line silhouette.', body: [
        'An A-line skirt is fitted near the waist and widens gradually toward the hem. Fabric behaviour determines whether that line looks crisp, fluid, or bulky.',
        'For a first version, a stable light-to-medium woven fabric is easier to measure, cut, press, and sew than a slippery or highly elastic fabric.',
        'Cotton poplin has enough structure to show the silhouette while remaining light enough to avoid a stiff, heavy result.',
        'Grain direction matters too. The straight grain normally runs parallel to the selvedge and gives a woven garment predictable stability. Cutting off-grain can make a hem twist or a side seam swing forward.',
        'Drape describes how cloth hangs and moves. Crisp fabric holds a clearer geometric outline; fluid fabric falls close to the body and produces softer movement. Neither is automatically better—the design must use the behaviour intentionally.',
        'Before cutting, test a swatch by folding, gathering, pressing, and holding it against the body. Record what the fabric does rather than relying only on its fibre name.',
        'A successful beginner plan joins material and method: stable fabric, accurate grain placement, suitable seam and hem finishes, and a silhouette that the chosen cloth can support.'
      ]},
      assessments: [
        choice('fabric-choice', 'Layer 1: Choose the foundation fabric', 'Choose the most beginner-friendly fabric for a first A-line skirt, then explain how it supports the silhouette.', {
          'cotton-poplin': 'Cotton poplin', 'silk-charmeuse': 'Silk charmeuse', 'heavy-denim': 'Heavy denim'
        }, 'cotton-poplin', ['stable', 'stability', 'weight', 'light', 'drape', 'structure', 'beginner', 'silhouette', 'sew', 'cut', 'press']),
        written('fabric-behaviour', 'Layer 2: Read fabric behaviour', 'Describe two simple swatch tests you would use before cutting, and explain what each test reveals about the fabric.', ['swatch','fold','gather','press','drape','stretch','hang','weight','structure','movement'], 50, 420),
        choice('grain-direction', 'Layer 3: Protect the grain', 'How should the centre line of a basic woven A-line skirt usually be positioned before cutting?', {
          'parallel-selvedge': 'Parallel to the selvedge on the straight grain',
          'diagonal-bias': 'Diagonally across the bias for maximum stretch',
          'across-crossgrain': 'Across the crossgrain regardless of the pattern marking'
        }, 'parallel-selvedge', ['grain','straight','selvedge','stable','stability','hang','twist','parallel','alignment']),
        choice('silhouette-match', 'Layer 4: Match drape to silhouette', 'Which pairing best creates a clear but comfortable beginner A-line silhouette?', {
          'poplin-clear-line': 'Light-to-medium cotton poplin with a controlled, clear line',
          'charmeuse-crisp-line': 'Very fluid silk charmeuse expected to hold a crisp geometric line',
          'denim-soft-flow': 'Heavy rigid denim expected to create soft flowing movement'
        }, 'poplin-clear-line', ['drape','structure','clear','line','a-line','silhouette','light','medium','controlled']),
        written('construction-plan', 'Layer 5: Plan before cutting', 'Write a short preparation plan covering grain placement, seam finishing, pressing, and a suitable hem for the skirt.', ['grain','selvedge','seam','finish','press','hem','allowance','pin','cut','mark'], 80, 600),
        written('foundation-rationale', 'Final project: Fabric-to-silhouette rationale', 'Write a mini design rationale for the finished A-line skirt. Connect the wearer or purpose, fabric weight and drape, grain placement, silhouette, and construction plan. Include one unsuitable fabric you rejected and why.', ['wearer','purpose','fabric','weight','drape','grain','silhouette','a-line','seam','hem','press','rejected','unsuitable'], 160, 1200, 'essay')
      ]
    },
    'fashion-design-studio': {
      id: 'fashion-design-studio', title: 'Fashion Design Studio: Concept to Collection',
      description: 'Turn an observation into a coherent fashion concept using colour, silhouette, and a focused mood board.',
      hero: 'public/images/courses/fashion-design-studio.webp',
      lesson: { title: 'Building a Clear Design Direction', objective: 'Translate one source of inspiration into a concise design direction with intentional visual choices.', body: [
        'Strong fashion concepts begin with a specific observation rather than a broad trend. Notice a repeated line, texture, colour relationship, or movement.',
        'Reduce the idea to three anchors: a silhouette direction, a limited colour story, and one material quality.',
        'A useful mood board supports decisions. Remove attractive images that do not clarify shape, atmosphere, surface, or colour.'
      ]},
      assessments: [
        choice('design-signal', 'Layer 1: Find the design signal', 'Which observation gives the clearest starting signal for a coastal-wind collection?', {'repeated-curves':'Repeated curved lines in wind-shaped dunes','everything-coastal':'Every image associated with a beach','current-trends':'A list of unrelated current trends'}, 'repeated-curves', ['line','curve','movement','repeat','specific','shape','wind']),
        choice('colour-story', 'Layer 2: Edit the colour story', 'Which palette best supports a coherent coastal-wind direction?', {'sand-blue-foam':'Sand, deep blue, and foam white','rainbow':'Every hue at equal intensity','neon-metallic':'Neon pink, chrome, and bright orange'}, 'sand-blue-foam', ['palette','colour','color','coastal','limited','coherent','sand','blue','foam']),
        written('silhouette-analysis', 'Layer 3: Silhouette analysis', 'Describe how shape and movement could express coastal wind in a garment.', ['silhouette','shape','line','layer','flow','movement','volume','drape','wind']),
        written('material-direction', 'Layer 4: Material direction', 'Recommend a material quality for the concept and explain what it contributes.', ['material','fabric','light','texture','drape','sheer','fluid','movement','structure']),
        choice('moodboard-edit', 'Layer 5: Mood-board edit', 'Which mood-board approach is ready to guide a collection?', {'coastal-movement':'Flowing layers, a sand-and-blue palette, and lightweight textured cloth','mixed-trends':'Neon tailoring, floral sportswear, metallic eveningwear, and denim basics','logo-study':'A board made only from fashion-brand logos'}, 'coastal-movement', ['coherent','flow','movement','palette','material','texture','coastal','focused'], 20, 320),
        written('collection-rationale', 'Final essay: Collection rationale', 'Connect inspiration, silhouette, colour, and material into one collection direction. Include one choice you deliberately excluded.', ['inspiration','silhouette','shape','colour','color','palette','material','fabric','excluded','removed','coherent','collection'], 120, 1200, 'essay')
      ]
    },
    'sewing-video-class': {
      id: 'sewing-video-class', title: 'Sewing Skills: Machine Control to Finishing',
      description: 'A guided learning path through machine handling, stitching, closures, shaping, and finishing techniques.',
      hero: 'assets/sewing-bodice-34.png',
      lesson: { title: 'How to Use the Sewing Practice Path', objective: 'Practise one technique at a time, explain the key control point, and unlock the next topic.', body: [
        'Work with your machine switched off when handling the needle area. Reproduce each setup before sewing.',
        'After every topic, submit a short observation. A response identifying the important control point unlocks the next topic.',
        'Your learning assistant can navigate and prepare responses, but only you can confirm practical work and submit it.'
      ]}, assessments: sewingTopics
    }
  };
}());
