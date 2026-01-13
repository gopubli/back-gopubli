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
        // Criar administrador
        $admin = Administrator::create([
            'name' => 'Administrador GO Publi',
            'email' => 'admin@gopubli.com',
            'password' => bcrypt('admin123'),
            'phone' => '11999999999',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        echo "✅ Administrador criado: admin@gopubli.com / admin123\n";

        // Criar empresas
        $company1 = Company::create([
            'name' => 'TechCorp Brasil',
            'email' => 'contato@techcorp.com.br',
            'password' => bcrypt('password123'),
            'cnpj' => '12345678000190',
            'phone' => '11987654321',
            'address' => 'Av. Paulista, 1000 - São Paulo, SP',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $company2 = Company::create([
            'name' => 'Fashion Store',
            'email' => 'contato@fashionstore.com.br',
            'password' => bcrypt('password123'),
            'cnpj' => '98765432000100',
            'phone' => '11876543210',
            'address' => 'Rua Oscar Freire, 500 - São Paulo, SP',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        // Criar carteiras GO Coin para empresas
        $company1->goCoinWallet()->create([
            'balance' => 500.00,
            'total_earned' => 500.00,
            'total_spent' => 0,
        ]);

        $company2->goCoinWallet()->create([
            'balance' => 300.00,
            'total_earned' => 300.00,
            'total_spent' => 0,
        ]);

        // Aceitar termos para empresas
        TermsAcceptance::recordAcceptance($company1, 'confidentiality', '1.0');
        TermsAcceptance::recordAcceptance($company2, 'confidentiality', '1.0');

        // Criar assinaturas para empresas
        $subscription1 = Subscription::create([
            'company_id' => $company1->id,
            'monthly_amount' => 200.00,
            'status' => 'pending',
        ]);
        $subscription1->activate();

        $subscription2 = Subscription::create([
            'company_id' => $company2->id,
            'monthly_amount' => 200.00,
            'status' => 'pending',
        ]);
        $subscription2->activate();

        // Criar influencers
        $influencer1 = Influencer::create([
            'name' => 'Maria Silva',
            'email' => 'maria@influencer.com',
            'password' => bcrypt('password123'),
            'cpf' => '12345678900',
            'phone' => '11998765432',
            'instagram' => '@maria_silva',
            'tiktok' => '@mariasilva',
            'youtube' => 'Maria Silva',
            'bio' => 'Criadora de conteúdo focada em tecnologia e inovação. 50k seguidores.',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $influencer2 = Influencer::create([
            'name' => 'João Santos',
            'email' => 'joao@influencer.com',
            'password' => bcrypt('password123'),
            'cpf' => '98765432100',
            'phone' => '11987654321',
            'instagram' => '@joao_santos',
            'tiktok' => '@joaosantos',
            'youtube' => 'João Santos',
            'bio' => 'Influencer de moda e lifestyle. 100k seguidores.',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $influencer3 = Influencer::create([
            'name' => 'Ana Costa',
            'email' => 'ana@influencer.com',
            'password' => bcrypt('password123'),
            'cpf' => '45678912300',
            'phone' => '11976543210',
            'instagram' => '@ana_costa',
            'tiktok' => '@anacosta',
            'bio' => 'Especialista em beleza e cosméticos. 75k seguidores.',
            'active' => true,
            'email_verified_at' => now(),
        ]);

        // Criar carteiras GO Coin para influencers
        $influencer1->goCoinWallet()->create([
            'balance' => 150.00,
            'total_earned' => 150.00,
            'total_spent' => 0,
        ]);

        $influencer2->goCoinWallet()->create([
            'balance' => 250.00,
            'total_earned' => 350.00,
            'total_spent' => 100.00,
        ]);

        $influencer3->goCoinWallet()->create([
            'balance' => 0,
            'total_earned' => 0,
            'total_spent' => 0,
        ]);

        // Aceitar termos para influencers
        TermsAcceptance::recordAcceptance($influencer1, 'confidentiality', '1.0');
        TermsAcceptance::recordAcceptance($influencer2, 'confidentiality', '1.0');
        TermsAcceptance::recordAcceptance($influencer3, 'confidentiality', '1.0');

        // Criar campanhas
        $campaign1 = Campaign::create([
            'company_id' => $company1->id,
            'title' => 'Lançamento de Novo App',
            'description' => 'Campanha para divulgação do nosso novo aplicativo de produtividade. Buscamos influencers de tecnologia.',
            'objective' => 'conversion',
            'amount' => 500.00,
            'min_amount' => 200.00,
            'status' => 'draft',
            'payment_status' => 'pending',
        ]);
        $campaign1->calculateDistribution();
        $campaign1->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'status' => 'open',
        ]);

        $campaign2 = Campaign::create([
            'company_id' => $company2->id,
            'title' => 'Coleção Verão 2026',
            'description' => 'Divulgação da nova coleção de verão. Procuramos influencers de moda.',
            'objective' => 'branding',
            'amount' => 800.00,
            'min_amount' => 200.00,
            'status' => 'draft',
            'payment_status' => 'pending',
        ]);
        $campaign2->calculateDistribution();
        $campaign2->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'status' => 'open',
        ]);

        $campaign3 = Campaign::create([
            'company_id' => $company1->id,
            'title' => 'Black Friday Tech',
            'description' => 'Campanha especial de Black Friday com descontos em tecnologia.',
            'objective' => 'traffic',
            'amount' => 1000.00,
            'min_amount' => 200.00,
            'status' => 'draft',
            'payment_status' => 'pending',
        ]);
        $campaign3->calculateDistribution();
        $campaign3->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'status' => 'in_progress',
            'selected_influencer_id' => $influencer1->id,
            'started_at' => now(),
        ]);

        // Criar candidaturas
        CampaignApplication::create([
            'campaign_id' => $campaign1->id,
            'influencer_id' => $influencer1->id,
            'offered_amount' => 280.00,
            'proposal_message' => 'Tenho experiência com conteúdo tech e engajamento alto!',
            'status' => 'pending',
        ]);

        CampaignApplication::create([
            'campaign_id' => $campaign1->id,
            'influencer_id' => $influencer3->id,
            'offered_amount' => 250.00,
            'proposal_message' => 'Adoraria participar desta campanha!',
            'status' => 'pending',
        ]);

        CampaignApplication::create([
            'campaign_id' => $campaign2->id,
            'influencer_id' => $influencer2->id,
            'offered_amount' => 450.00,
            'proposal_message' => 'Sou especialista em moda e tenho grande audiência no nicho!',
            'status' => 'pending',
        ]);

        // Campanha já aceita
        CampaignApplication::create([
            'campaign_id' => $campaign3->id,
            'influencer_id' => $influencer1->id,
            'offered_amount' => 600.00,
            'proposal_message' => 'Tenho histórico excelente em campanhas tech!',
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $this->command->info('✅ Dados de exemplo criados com sucesso!');
        $this->command->info('');
        $this->command->info('📊 Resumo:');
        $this->command->info('   - 2 Empresas criadas');
        $this->command->info('   - 3 Influencers criados');
        $this->command->info('   - 3 Campanhas criadas');
        $this->command->info('   - 4 Candidaturas criadas');
        $this->command->info('');
        $this->command->info('🔑 Credenciais de Teste:');
        $this->command->info('');
        $this->command->info('   Empresa 1:');
        $this->command->info('   Email: contato@techcorp.com.br');
        $this->command->info('   Senha: password123');
        $this->command->info('');
        $this->command->info('   Empresa 2:');
        $this->command->info('   Email: contato@fashionstore.com.br');
        $this->command->info('   Senha: password123');
        $this->command->info('');
        $this->command->info('   Influencer 1:');
        $this->command->info('   Email: maria@influencer.com');
        $this->command->info('   Senha: password123');
        $this->command->info('');
        $this->command->info('   Influencer 2:');
        $this->command->info('   Email: joao@influencer.com');
        $this->command->info('   Senha: password123');
        $this->command->info('');
        $this->command->info('   Influencer 3:');
        $this->command->info('   Email: ana@influencer.com');
        $this->command->info('   Senha: password123');
    }
}
