const express = require('express');
const methodOverride = require('method-override');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Serve static files from public directory
app.use(express.static(path.join(__dirname, 'public')));

// Debug middleware for static files
app.use((req, res, next) => {
    if (req.url.startsWith('/css/') || req.url.startsWith('/js/') || req.url.startsWith('/images/')) {
        console.log('Static file request:', req.url, '-', res.statusCode);
    }
    next();
});

app.use(express.urlencoded({ extended: true }));
app.use(methodOverride('_method'));

// In-memory data storage
let posts = [
  {
    id: 1,
    title: "Discover the Beauty of Lake Malawi",
    content: "Lake Malawi, also known as the Lake of Stars, is one of Africa's most beautiful freshwater lakes. With its crystal-clear waters and diverse aquatic life, it's a paradise for snorkelers and beach lovers alike. The lake is home to more species of fish than any other lake in the world, including hundreds of species of colorful cichlids.",
    createdAt: new Date('2024-07-15'),
    image: "lake-malawi.jpg"
  },
  {
    id: 2,
    title: "Hiking Mount Mulanje",
    content: "Mount Mulanje, also known as the 'Island in the Sky', is the highest mountain in Malawi, rising to 3,002 meters at its highest point. The massif offers spectacular hiking opportunities through lush forests, past stunning waterfalls, and up to breathtaking viewpoints. Experienced guides are available to lead you on multi-day treks through this magnificent landscape.",
    createdAt: new Date('2024-07-10'),
    image: "mount-mulanje.jpg"
  },
  {
    id: 3,
    title: "Wildlife in Liwonde National Park",
    content: "Liwonde National Park is one of Malawi's premier wildlife destinations, home to elephants, hippos, crocodiles, and a growing population of rhinos. The Shire River flows through the park, providing excellent opportunities for boat safaris where visitors can see wildlife coming to drink at the water's edge.",
    createdAt: new Date('2024-07-05'),
    image: "liwonde-park.jpg"
  }
];
let nextId = 4;

// Routes
// Home page - display all posts
app.get('/', (req, res) => {
  res.render('index', { 
    posts: posts.sort((a, b) => b.createdAt - a.createdAt),
    title: 'Malawi Tourism Blog - Discover the Warm Heart of Africa'
  });
});

// View single post
app.get('/posts/:id', (req, res) => {
  const post = posts.find(p => p.id === parseInt(req.params.id));
  if (!post) {
    return res.status(404).render('error', { 
      message: 'Post not found',
      title: 'Post Not Found - Malawi Tourism Blog'
    });
  }
  res.render('post', { 
    post,
    title: `${post.title} - Malawi Tourism Blog`
  });
});

// Create new post - form
app.get('/posts/new', (req, res) => {
  res.render('create', { 
    title: 'Create New Post - Malawi Tourism Blog',
    post: {} 
  });
});

// Create new post - process form
app.post('/posts', (req, res) => {
  const { title, content } = req.body;
  
  // Simple validation
  if (!title || !content) {
    return res.status(400).render('create', { 
      title: 'Create New Post - Malawi Tourism Blog',
      post: { title, content },
      error: 'Both title and content are required' 
    });
  }
  
  const newPost = {
    id: nextId++,
    title,
    content,
    createdAt: new Date(),
    image: "default.jpg"
  };
  
  posts.push(newPost);
  res.redirect('/');
});

// Edit post - form
app.get('/posts/:id/edit', (req, res) => {
  const post = posts.find(p => p.id === parseInt(req.params.id));
  if (!post) {
    return res.status(404).render('error', { 
      message: 'Post not found',
      title: 'Post Not Found - Malawi Tourism Blog'
    });
  }
  res.render('edit', { 
    post,
    title: `Edit ${post.title} - Malawi Tourism Blog`
  });
});

// Edit post - process form
app.put('/posts/:id', (req, res) => {
  const { title, content } = req.body;
  const postIndex = posts.findIndex(p => p.id === parseInt(req.params.id));
  
  if (postIndex === -1) {
    return res.status(404).render('error', { 
      message: 'Post not found',
      title: 'Post Not Found - Malawi Tourism Blog'
    });
  }
  
  // Simple validation
  if (!title || !content) {
    return res.status(400).render('edit', { 
      post: { id: req.params.id, title, content },
      title: `Edit Post - Malawi Tourism Blog`,
      error: 'Both title and content are required' 
    });
  }
  
  posts[postIndex] = { ...posts[postIndex], title, content };
  res.redirect(`/posts/${req.params.id}`);
});

// Delete post
app.delete('/posts/:id', (req, res) => {
  const postIndex = posts.findIndex(p => p.id === parseInt(req.params.id));
  
  if (postIndex === -1) {
    return res.status(404).render('error', { 
      message: 'Post not found',
      title: 'Post Not Found - Malawi Tourism Blog'
    });
  }
  
  posts.splice(postIndex, 1);
  res.redirect('/');
});

// Test route to check if CSS is working
app.get('/test-css', (req, res) => {
  res.send(`
    <!DOCTYPE html>
    <html>
    <head>
        <title>CSS Test</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <div class="container">
            <h1 style="color: var(--primary-green)">CSS Test Page</h1>
            <p>If this text is green, CSS is loading correctly.</p>
            <p>Current time: ${new Date()}</p>
            <a href="/">Back to Home</a>
        </div>
    </body>
    </html>
  `);
});

// Start server
app.listen(PORT, () => {
  console.log(`Malawi Tourism Blog server running on http://localhost:${PORT}`);
  console.log('CSS test page: http://localhost:3000/test-css');
  console.log('Static files directory:', path.join(__dirname, 'public'));
});