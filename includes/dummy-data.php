<?php
/* DUMMY DATA – backend phase mein yeh functions database queries se replace honge.
   Array ki shape wahi rahegi, is liye templates change nahi honge. */
function mk(string $t, string $c, string $a, string $date, int $min, string $x, string $img, string $kicker = '', int $views = 0): array {
    return ['title'=>$t,'slug'=>slugify($t),'category'=>$c,'category_slug'=>slugify($c),'author'=>$a,'date'=>$date,'read'=>$min,
        'excerpt'=>$x,'image'=>BASE_URL . 'assets/images/' . $img . '.jpg','kicker'=>$kicker ?: $c,'views'=>$views];
}
function dummy_ticker(): array { return ['label'=>'Dispatch','text'=>'DeepSeek-V3 release benchmarks show 38% compute reduction against Llama','url'=>BASE_URL.'blog/']; }
function dummy_featured(): array {
    return mk('The 2025 State of Frontier Models: Architecture Shifts from Transformers to State-Space Hybrid Systems','Artificial Intelligence','Dr. Marcus Vance','2025-10-24',9,
      'A rigorous deep dive into next-generation reasoning architectures, latency benchmarks on edge silicon, and how open-weights models are dismantling proprietary moats across enterprise workflows.','hero','Deep Report') + ['role'=>'Senior AI Research Lead','initials'=>'MV'];
}
function dummy_radar(): array { return [
    ['kicker'=>'Inference Speedup','title'=>'vLLM v0.6 Kernel Optimizations Drop P99 Time-to-First-Token by 44%','meta'=>'Latency Lab','read'=>4,'url'=>'#'],
    ['kicker'=>'Dev Stack Audit','title'=>'Why 68% of Infrastructure Teams Migrated from Terraform to OpenTofu This Quarter','meta'=>'DevOps Registry','read'=>6,'url'=>'#'],
    ['kicker'=>'Chip Hardware','title'=>'Cerebras CS-3 vs NVIDIA B200: Real Silicon Memory Bandwidth Profiles','meta'=>'Hardware Desk','read'=>11,'url'=>'#'],
]; }
function dummy_latest(): array { return [
    mk('Next.js 15 Server Actions vs API Routes: Cold-Start Benchmarks at Scale','Web Development','Sarah Jenkins','2025-10-24',6,'Empirical testing across 10,000 concurrent Edge requests shows unexpected execution overhead in server action serialization.','l1'),
    mk('Cursor vs Claude Dev vs Windsurf: An Exhaustive Developer Workflow Audit','AI Tools','Alex Thorne','2025-10-23',8,'A comprehensive head-to-head comparison evaluating multi-file refactoring accuracy, context handling, and latency.','l2'),
    mk('Dockerizing Local LLMs with Ollama and vLLM on Apple Silicon','Software','David K. Miller','2025-10-23',11,'Step-by-step production containerization leveraging Metal MPS acceleration and zero-copy memory sharing.','l3'),
    mk('Automating Technical Documentation Pipelines with GitHub Actions & LLMs','Automation','Elena Rostova','2025-10-22',5,'How our engineering team automated versioned API document sync, type extraction, and review gating.','l4'),
    mk("Core Web Vitals in the AI Overview Era: How Search Intent Has Shifted",'Blogging & SEO',"Liam O'Connor",'2025-10-21',7,'Analyzing over 500,000 SERP impressions to quantify the traffic distribution impact of generative answers.','l5'),
    mk('Building a Distraction-Free Terminal Workspace with Neovim, Tmux, and Zsh','Productivity','Kenji Sato','2025-10-20',10,'A minimalist, reproducible dotfiles architecture engineered for sustained deep work.','l6'),
]; }
function dummy_topics(): array { return [
    ['name'=>'AI Tools','slug'=>'ai-tools','icon'=>'bi-robot','desc'=>'Evaluations of code assistants, vision models, and LLM orchestration frameworks.','count'=>128],
    ['name'=>'Artificial Intelligence','slug'=>'artificial-intelligence','icon'=>'bi-person-gear','desc'=>'Frontier research, model architectures, alignment, and cognitive systems.','count'=>215],
    ['name'=>'Technology','slug'=>'technology','icon'=>'bi-cpu','desc'=>'Hardware innovations, cloud infrastructure, chipsets, and computing paradigm shifts.','count'=>194],
    ['name'=>'Web Development','slug'=>'web-development','icon'=>'bi-terminal','desc'=>'Modern fullstack stacks, frontend engineering, edge compute, and API architecture.','count'=>280],
    ['name'=>'Software & Productivity','slug'=>'software-productivity','icon'=>'bi-layers','desc'=>'Developer tooling, operating systems, IDE workflows, and focus ergonomics.','count'=>165],
    ['name'=>'Automation','slug'=>'automation','icon'=>'bi-arrow-repeat','desc'=>'CI/CD pipelines, autonomous agents, Python scripting, and webhook orchestration.','count'=>112],
    ['name'=>'Blogging & SEO','slug'=>'blogging-seo','icon'=>'bi-graph-up','desc'=>'Search engine algorithms, technical content architecture, schema protocols, and algorithmic performance audits.','count'=>98],
]; }
function dummy_popular(): array { return [
    mk('The Practical Guide to Fine-Tuning Llama 3.3 on Custom Datasets','AI Engineering','','2025-10-19',12,'','p1','',48200),
    mk('Zero-Allocation Memory Patterns in Go 1.24 High-Throughput Microservices','Software','','2025-10-17',9,'','p2','',39800),
    mk('Why We Rewrote Our Core Ingestion Engine from Node.js to Rust: 1 Year Later','Programming','','2025-10-14',15,'','p3','',34100),
    mk('Kubernetes vs Nomad: An Honest Multi-Cluster Cost Comparison','DevOps','','2025-10-11',8,'','p4','',29500),
    mk('Vector Search at Billion-Scale: pgvector vs Qdrant vs Milvus','Databases','','2025-10-08',14,'','p5','',27000),
]; }
function dummy_ai_chips(): array { return ['All AI','AI Tools','ChatGPT & LLMs','Generative AI','AI Productivity','AI Tutorials']; }
function dummy_ai(): array { return [
    mk('DeepSeek-V3 vs Llama 3: Complete Parameter Activation Profiles','AI Tools','','2025-10-24',7,'Evaluating FP8 multi-token prediction pipelines, memory footprint constraints, and coding accuracy.','ai1','Benchmark Audit'),
    mk('Building Reliable Multi-Agent Teams using Structured Outputs','AI Tools','','2025-10-23',10,'Eliminating non-deterministic hallucinations through structured output schemas and cycle detection.','ai2','Agent Systems'),
    mk('Automating Customer Support Analysis with Local LLMs','AI Tools','','2025-10-22',8,'A private on-premise pipeline parsing 50k feedback tickets weekly with zero third-party API calls.','ai3','Enterprise RAG'),
    mk('Prompt Engineering for Code Generation: 14 Rules Senior Engineers Use','AI Tools','','2025-10-21',12,'How few-shot formatting, chain-of-thought scaffolds, and schema pinning dramatically enhance output.','ai4','Practice Rules'),
]; }
function dummy_tech(): array { return [
    mk('Why WebAssembly (Wasm) is Quietly Replacing Microservices for Edge Compute','Technology','','2025-10-24',9,'Sub-millisecond cold boot times and rock-solid sandbox isolation are redefining serverless architecture.','t1','Cloud Architecture'),
    mk('The Death of Monolithic CSS: How CSS Variables and Container Queries Won','Web Development','','2025-10-22',6,'Why native web APIs rendered massive utility builds redundant across our large-scale design systems.','t2','Frontend Engineering'),
    mk('Understanding PostgreSQL 17 Query Optimizer Improvements Under High Concurrency','Technology','','2025-10-20',11,'Deep dive into adaptive lock escalation, parallel sequential scan changes, and planner statistics.','t3','Database Engines'),
    mk('Git Worktrees vs Stashing: A Masterclass in Multi-Branch Context Switching','Software','','2025-10-18',5,'Stop stash-popping dirty working trees. How isolated worktree checkouts will accelerate your workflow.','t4','Developer Tooling'),
]; }
