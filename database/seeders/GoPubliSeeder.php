<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Subscription;
use App\Models\TermsAcceptance;
use App\Models\GoCoinWallet;

class GoPubliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed do GoPubLi...');
        
        // Criar administrador
        $admin = Administrator::create([
            'name' => 'Administrador GO Publi',
            'email' => 'admin@gopubli.com',
            'password' => bcrypt('admin123'),
            'phone' => '11999999999',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Administrador criado');

        // Criar empresas
        $companies = [
            [
                'name' => 'TechCorp Brasil',
                'email' => 'contato@techcorp.com.br',
                'cnpj' => '12345678000190',
                'phone' => '11987654321',
                'address' => 'Av. Paulista, 1000 - São Paulo, SP',
                'balance' => 1500.00,
            ],
            [
                'name' => 'Fashion Store',
                'email' => 'contato@fashionstore.com.br',
                'cnpj' => '98765432000100',
                'phone' => '11876543210',
                'address' => 'Rua Oscar Freire, 500 - São Paulo, SP',
                'balance' => 2000.00,
            ],
            [
                'name' => 'Beauty Lab',
                'email' => 'contato@beautylab.com.br',
                'cnpj' => '11223344000155',
                'phone' => '11965432100',
                'address' => 'Rua Augusta, 200 - São Paulo, SP',
                'balance' => 800.00,
            ],
            [
                'name' => 'Fitness Pro',
                'email' => 'contato@fitnesspro.com.br',
                'cnpj' => '55667788000199',
                'phone' => '11954321098',
                'address' => 'Av. Rebouças, 1500 - São Paulo, SP',
                'balance' => 1200.00,
            ],
            [
                'name' => 'Gourmet Foods',
                'email' => 'contato@gourmetfoods.com.br',
                'cnpj' => '99887766000144',
                'phone' => '11943210987',
                'address' => 'Rua dos Pinheiros, 800 - São Paulo, SP',
                'balance' => 600.00,
            ],
        ];

        $createdCompanies = [];
        foreach ($companies as $companyData) {
            $balance = $companyData['balance'];
            unset($companyData['balance']);
            
            $company = Company::create(array_merge($companyData, [
                'password' => bcrypt('password123'),
                'active' => true,
                'email_verified_at' => now(),
            ]));

            $company->goCoinWallet()->create([
                'balance' => $balance,
                'total_earned' => $balance,
                'total_spent' => 0,
            ]);

            TermsAcceptance::recordAcceptance($company, 'confidentiality', '1.0');

            $subscription = Subscription::create([
                'company_id' => $company->id,
                'monthly_amount' => 200.00,
                'status' => 'pending',
            ]);
            $subscription->activate();

            $createdCompanies[] = $company;
        }

        $this->command->info('✅ 5 Empresas criadas');

        // Criar influencers
        $influencers = [
            [
                'name' => 'Maria Silva',
                'email' => 'maria@influencer.com',
                'cpf' => '12345678900',
                'phone' => '11998765432',
                'instagram' => '@maria_silva',
                'tiktok' => '@mariasilva',
                'youtube' => 'Maria Silva',
                'bio' => 'Criadora de conteúdo focada em tecnologia e inovação. 50k seguidores.',
                'balance' => 150.00,
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao@influencer.com',
                'cpf' => '98765432100',
                'phone' => '11987654321',
                'instagram' => '@joao_santos',
                'tiktok' => '@joaosantos',
                'youtube' => 'João Santos',
                'bio' => 'Influencer de moda e lifestyle. 100k seguidores.',
                'balance' => 250.00,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana@influencer.com',
                'cpf' => '45678912300',
                'phone' => '11976543210',
                'instagram' => '@ana_costa',
                'tiktok' => '@anacosta',
                'bio' => 'Especialista em beleza e cosméticos. 75k seguidores.',
                'balance' => 320.00,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@influencer.com',
                'cpf' => '78912345600',
                'phone' => '11965432198',
                'instagram' => '@pedro_oliveira',
                'tiktok' => '@pedrooliveira',
                'youtube' => 'Pedro Oliveira Fit',
                'bio' => 'Personal trainer e criador de conteúdo fitness. 120k seguidores.',
                'balance' => 180.00,
            ],
            [
                'name' => 'Carla Mendes',
                'email' => 'carla@influencer.com',
                'cpf' => '32165498700',
                'phone' => '11954321876',
                'instagram' => '@carla_mendes',
                'tiktok' => '@carlamendes',
                'bio' => 'Food blogger e chef. Apaixonada por gastronomia. 85k seguidores.',
                'balance' => 90.00,
            ],
            [
                'name' => 'Lucas Ferreira',
                'email' => 'lucas@influencer.com',
                'cpf' => '65432198700',
                'phone' => '11943219876',
                'instagram' => '@lucas_ferreira',
                'youtube' => 'Lucas Tech',
                'bio' => 'Revisor de tecnologia e gadgets. 95k seguidores.',
                'balance' => 0,
            ],
            [
                'name' => 'Juliana Rocha',
                'email' => 'juliana@influencer.com',
                'cpf' => '98732165400',
                'phone' => '11932198765',
                'instagram' => '@juliana_rocha',
                'tiktok' => '@julianarocha',
                'bio' => 'Influenciadora de moda sustentável. 65k seguidores.',
                'balance' => 210.00,
            ],
            [
                'name' => 'Rafael Lima',
                'email' => 'rafael@influencer.com',
                'cpf' => '74185296300',
                'phone' => '11921987654',
                'instagram' => '@rafael_lima',
                'tiktok' => '@rafaellima',
                'youtube' => 'Rafael Lima Vlogs',
                'bio' => 'Lifestyle e viagens. 110k seguidores.',
                'balance' => 400.00,
            ],
        ];

        $createdInfluencers = [];
        foreach ($influencers as $influencerData) {
            $balance = $influencerData['balance'];
            unset($influencerData['balance']);
            
            $influencer = Influencer::create(array_merge($influencerData, [
                'password' => bcrypt('password123'),
                'active' => true,
                'email_verified_at' => now(),
            ]));

            $totalEarned = $balance + rand(0, 500);
            $influencer->goCoinWallet()->create([
                'balance' => $balance,
                'total_earned' => $totalEarned,
                'total_spent' => $totalEarned - $balance,
            ]);

            TermsAcceptance::recordAcceptance($influencer, 'confidentiality', '1.0');

            $createdInfluencers[] = $influencer;
        }

        $this->command->info('✅ 8 Influencers criados');

        $this->command->info('✅ 8 Influencers criados');

        // Criar campanhas
        $campaigns = [
            [
                'company' => $createdCompanies[0], // TechCorp
                'title' => 'Lançamento de Novo App de Produtividade',
                'description' => 'Campanha para divulgação do nosso novo aplicativo de produtividade. Buscamos influencers de tecnologia com audiência engajada.',
                'objective' => 'conversion',
                'category' => 'technology',
                'platform' => 'instagram',
                'amount' => 800.00,
                'min_amount' => 200.00,
                'requirements' => 'Mínimo 30k seguidores, engajamento acima de 3%',
                'deliverables' => '3 posts no feed, 5 stories',
                'status' => 'open',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(20),
            ],
            [
                'company' => $createdCompanies[1], // Fashion Store
                'title' => 'Coleção Verão 2026',
                'description' => 'Divulgação da nova coleção de verão. Procuramos influencers de moda com estilo autêntico.',
                'objective' => 'branding',
                'category' => 'fashion',
                'platform' => 'instagram',
                'amount' => 1200.00,
                'min_amount' => 200.00,
                'requirements' => 'Influencers de moda, mínimo 50k seguidores',
                'deliverables' => '4 posts, 8 stories, 1 reel',
                'status' => 'open',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addDays(25),
            ],
            [
                'company' => $createdCompanies[2], // Beauty Lab
                'title' => 'Lançamento Nova Linha de Skincare',
                'description' => 'Campanha de lançamento da nossa linha premium de skincare. Buscamos criadores de conteúdo de beleza.',
                'objective' => 'branding',
                'category' => 'beauty',
                'platform' => 'instagram',
                'amount' => 600.00,
                'min_amount' => 200.00,
                'requirements' => 'Influencers de beleza, pele saudável',
                'deliverables' => '2 posts, 6 stories, review completo',
                'status' => 'open',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(30),
            ],
            [
                'company' => $createdCompanies[3], // Fitness Pro
                'title' => 'Desafio 30 Dias Fitness',
                'description' => 'Campanha de desafio fitness de 30 dias. Procuramos influencers fit com comunidade ativa.',
                'objective' => 'branding',
                'category' => 'fitness',
                'platform' => 'instagram',
                'amount' => 900.00,
                'min_amount' => 200.00,
                'requirements' => 'Influencers fitness, mínimo 40k seguidores',
                'deliverables' => 'Posts diários durante 7 dias, stories',
                'status' => 'in_progress',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'selected_influencer_id' => $createdInfluencers[3]->id,
                'started_at' => now()->subDays(5),
            ],
            [
                'company' => $createdCompanies[4], // Gourmet Foods
                'title' => 'Receitas Gourmet para o Dia a Dia',
                'description' => 'Divulgação de linha de produtos gourmet. Buscamos food bloggers criativos.',
                'objective' => 'traffic',
                'category' => 'food',
                'platform' => 'instagram',
                'amount' => 500.00,
                'min_amount' => 200.00,
                'requirements' => 'Food bloggers, boas fotos de comida',
                'deliverables' => '3 receitas, 6 stories, 1 vídeo',
                'status' => 'open',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(35),
            ],
            [
                'company' => $createdCompanies[0], // TechCorp
                'title' => 'Black Friday Tech 2026',
                'description' => 'Campanha especial de Black Friday com descontos em tecnologia. Grande oportunidade!',
                'objective' => 'conversion',
                'category' => 'technology',
                'platform' => 'instagram',
                'amount' => 1500.00,
                'min_amount' => 200.00,
                'requirements' => 'Alta audiência tech, engajamento comprovado',
                'deliverables' => '5 posts, 10 stories, 2 reels',
                'status' => 'completed',
                'start_date' => now()->subDays(30),
                'end_date' => now()->subDays(5),
                'selected_influencer_id' => $createdInfluencers[0]->id,
                'started_at' => now()->subDays(30),
                'completed_at' => now()->subDays(5),
            ],
            [
                'company' => $createdCompanies[1], // Fashion Store
                'title' => 'Moda Sustentável',
                'description' => 'Campanha focada em moda consciente e sustentável.',
                'objective' => 'branding',
                'category' => 'fashion',
                'platform' => 'instagram',
                'amount' => 700.00,
                'min_amount' => 200.00,
                'requirements' => 'Influencers alinhados com sustentabilidade',
                'deliverables' => '3 posts educativos, 7 stories',
                'status' => 'open',
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(45),
            ],
            [
                'company' => $createdCompanies[2], // Beauty Lab
                'title' => 'Rotina de Beleza Matinal',
                'description' => 'Campanha mostrando rotina de cuidados pela manhã com nossos produtos.',
                'objective' => 'branding',
                'category' => 'beauty',
                'platform' => 'tiktok',
                'amount' => 400.00,
                'min_amount' => 200.00,
                'requirements' => 'Criadores de conteúdo TikTok',
                'deliverables' => '5 vídeos curtos, tutoriais',
                'status' => 'draft',
                'start_date' => now()->addDays(20),
                'end_date' => now()->addDays(50),
            ],
        ];

        $createdCampaigns = [];
        foreach ($campaigns as $campaignData) {
            $company = $campaignData['company'];
            unset($campaignData['company']);
            
            $campaign = Campaign::create(array_merge($campaignData, [
                'company_id' => $company->id,
                'payment_status' => in_array($campaignData['status'], ['draft']) ? 'pending' : 'paid',
                'payment_confirmed_at' => in_array($campaignData['status'], ['draft']) ? null : now(),
            ]));
            
            if (!in_array($campaignData['status'], ['draft'])) {
                $campaign->calculateDistribution();
            }

            $createdCampaigns[] = $campaign;
        }

        $this->command->info('✅ 8 Campanhas criadas');

        // Criar candidaturas
        $applications = [
            // Campanha 1 - App Produtividade (TechCorp)
            ['campaign' => 0, 'influencer' => 0, 'amount' => 450.00, 'message' => 'Tenho experiência com conteúdo tech e engajamento alto! Já trabalhei com apps similares.', 'status' => 'pending'],
            ['campaign' => 0, 'influencer' => 5, 'amount' => 480.00, 'message' => 'Revisor de tecnologia profissional. Posso trazer análises detalhadas.', 'status' => 'pending'],
            
            // Campanha 2 - Coleção Verão (Fashion Store)
            ['campaign' => 1, 'influencer' => 1, 'amount' => 650.00, 'message' => 'Sou especialista em moda e tenho grande audiência no nicho! Amo trabalhar com marcas de moda.', 'status' => 'pending'],
            ['campaign' => 1, 'influencer' => 6, 'amount' => 580.00, 'message' => 'Moda sustentável é minha paixão. Minha audiência adora conteúdo fashion.', 'status' => 'pending'],
            ['campaign' => 1, 'influencer' => 7, 'amount' => 700.00, 'message' => 'Lifestyle influencer com forte presença em moda. Vamos arrasar juntos!', 'status' => 'pending'],
            
            // Campanha 3 - Skincare (Beauty Lab)
            ['campaign' => 2, 'influencer' => 2, 'amount' => 380.00, 'message' => 'Especialista em skincare com audiência super engajada!', 'status' => 'pending'],
            
            // Campanha 4 - Desafio Fitness (aceita)
            ['campaign' => 3, 'influencer' => 3, 'amount' => 600.00, 'message' => 'Personal trainer profissional! Vou criar um desafio incrível para minha comunidade.', 'status' => 'accepted'],
            
            // Campanha 5 - Receitas Gourmet
            ['campaign' => 4, 'influencer' => 4, 'amount' => 350.00, 'message' => 'Food blogger apaixonada! Minhas receitas sempre fazem sucesso.', 'status' => 'pending'],
            
            // Campanha 6 - Black Friday (concluída)
            ['campaign' => 5, 'influencer' => 0, 'amount' => 900.00, 'message' => 'Tenho histórico excelente em campanhas tech! Vamos vender muito!', 'status' => 'accepted'],
            
            // Campanha 7 - Moda Sustentável
            ['campaign' => 6, 'influencer' => 6, 'amount' => 450.00, 'message' => 'Sustentabilidade é meu foco principal! Tenho audiência consciente.', 'status' => 'pending'],
            ['campaign' => 6, 'influencer' => 1, 'amount' => 420.00, 'message' => 'Adoraria participar de uma campanha de moda consciente!', 'status' => 'pending'],
        ];

        foreach ($applications as $appData) {
            CampaignApplication::create([
                'campaign_id' => $createdCampaigns[$appData['campaign']]->id,
                'influencer_id' => $createdInfluencers[$appData['influencer']]->id,
                'offered_amount' => $appData['amount'],
                'proposal_message' => $appData['message'],
                'status' => $appData['status'],
                'accepted_at' => $appData['status'] === 'accepted' ? now() : null,
            ]);
        }

        $this->command->info('✅ 11 Candidaturas criadas');

        $this->command->info('✅ 11 Candidaturas criadas');
        
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Dados de exemplo criados com sucesso!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        
        $this->command->info('📊 <fg=cyan>RESUMO DOS DADOS CRIADOS:</fg=cyan>');
        $this->command->info('   • 1 Administrador');
        $this->command->info('   • 5 Empresas (com assinaturas ativas)');
        $this->command->info('   • 8 Influencers');
        $this->command->info('   • 8 Campanhas (vários status)');
        $this->command->info('   • 11 Candidaturas');
        $this->command->newLine();
        
        $this->command->info('🔐 <fg=yellow>CREDENCIAIS DE ACESSO:</fg=yellow>');
        $this->command->newLine();
        
        $this->command->info('   <fg=magenta>═══ ADMINISTRADOR ═══</fg=magenta>');
        $this->command->info('   Email: <fg=green>admin@gopubli.com</fg=green>');
        $this->command->info('   Senha: <fg=green>admin123</fg=green>');
        $this->command->newLine();
        
        $this->command->info('   <fg=magenta>═══ EMPRESAS ═══</fg=magenta>');
        $this->command->info('   1. <fg=cyan>TechCorp Brasil</fg=cyan>');
        $this->command->info('      Email: <fg=green>contato@techcorp.com.br</fg=green>');
        $this->command->info('      Senha: <fg=green>password123</fg=green>');
        $this->command->info('      Saldo: R$ 1.500,00');
        $this->command->newLine();
        
        $this->command->info('   2. <fg=cyan>Fashion Store</fg=cyan>');
        $this->command->info('      Email: <fg=green>contato@fashionstore.com.br</fg=green>');
        $this->command->info('      Senha: <fg=green>password123</fg=green>');
        $this->command->info('      Saldo: R$ 2.000,00');
        $this->command->newLine();
        
        $this->command->info('   3. <fg=cyan>Beauty Lab</fg=cyan>');
        $this->command->info('      Email: <fg=green>contato@beautylab.com.br</fg=green>');
        $this->command->info('      Senha: <fg=green>password123</fg=green>');
        $this->command->newLine();
        
        $this->command->info('   4. <fg=cyan>Fitness Pro</fg=cyan>');
        $this->command->info('      Email: <fg=green>contato@fitnesspro.com.br</fg=green>');
        $this->command->info('      Senha: <fg=green>password123</fg=green>');
        $this->command->newLine();
        
        $this->command->info('   5. <fg=cyan>Gourmet Foods</fg=cyan>');
        $this->command->info('      Email: <fg=green>contato@gourmetfoods.com.br</fg=green>');
        $this->command->info('      Senha: <fg=green>password123</fg=green>');
        $this->command->newLine();
        
        $this->command->info('   <fg=magenta>═══ INFLUENCERS ═══</fg=magenta>');
        $this->command->info('   1. Maria Silva (Tech) - <fg=green>maria@influencer.com</fg=green>');
        $this->command->info('   2. João Santos (Moda) - <fg=green>joao@influencer.com</fg=green>');
        $this->command->info('   3. Ana Costa (Beauty) - <fg=green>ana@influencer.com</fg=green>');
        $this->command->info('   4. Pedro Oliveira (Fitness) - <fg=green>pedro@influencer.com</fg=green>');
        $this->command->info('   5. Carla Mendes (Food) - <fg=green>carla@influencer.com</fg=green>');
        $this->command->info('   6. Lucas Ferreira (Tech) - <fg=green>lucas@influencer.com</fg=green>');
        $this->command->info('   7. Juliana Rocha (Moda) - <fg=green>juliana@influencer.com</fg=green>');
        $this->command->info('   8. Rafael Lima (Lifestyle) - <fg=green>rafael@influencer.com</fg=green>');
        $this->command->info('   Senha para todos: <fg=green>password123</fg=green>');
        $this->command->newLine();
        
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
