<?php

namespace Database\Factories;

use App\Models\SalesData;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesDataFactory extends Factory
{
    protected $model = SalesData::class;

    public function definition()
    {
        $products = [
            'Processador', 'Memória RAM', 'Placa de Vídeo', 'SSD', 'HDD', 'Fonte', 
            'Gabinete', 'Monitor', 'Teclado', 'Mouse', 'Placa-Mãe', 'Notebook', 
            'MacBook', 'iPhone', 'iPad', 'Tablet', 'Impressora', 'Scanner', 
            'Roteador', 'Modem', 'Headset', 'Carregador'
        ];

        $descriptions = [
            'Processador' => 'Processador de última geração com alto desempenho.',
            'Memória RAM' => 'Memória RAM de alta velocidade para melhor performance.',
            'Placa de Vídeo' => 'Placa de vídeo poderosa para jogos e edição.',
            'SSD' => 'SSD rápido e confiável para armazenamento.',
            'HDD' => 'HDD com grande capacidade de armazenamento.',
            'Fonte' => 'Fonte de alimentação eficiente e silenciosa.',
            'Gabinete' => 'Gabinete espaçoso e bem ventilado.',
            'Monitor' => 'Monitor com alta resolução e cores vibrantes.',
            'Teclado' => 'Teclado ergonômico e confortável.',
            'Mouse' => 'Mouse preciso e de alta sensibilidade.',
            'Placa-Mãe' => 'Placa-mãe compatível com as últimas tecnologias.',
            'Notebook' => 'Notebook leve e potente para trabalho e lazer.',
            'MacBook' => 'MacBook com design elegante e alto desempenho.',
            'iPhone' => 'iPhone com a mais nova tecnologia e câmeras avançadas.',
            'iPad' => 'iPad versátil e ideal para produtividade e entretenimento.',
            'Tablet' => 'Tablet com grande autonomia de bateria.',
            'Impressora' => 'Impressora rápida e econômica.',
            'Scanner' => 'Scanner de alta resolução para digitalizações precisas.',
            'Roteador' => 'Roteador com alta cobertura e estabilidade.',
            'Modem' => 'Modem rápido e confiável para conexão de internet.',
            'Carregador' => 'Carregador Power Turbo de 25W',
            'Headset'=> 'O Headset Gamer traz um visual simples, mas bastante elegante, esbanjando personalidade e modernidade.'
        ];

        $payment_methods = ['credit_card', 'bank_transfer', 'money', 'paypal'];

        $product = $this->faker->randomElement($products);

        return [
            'status' => $this->faker->randomElement(['A', 'C']),
            'product_name' => $product,
            'description' => $descriptions[$product],
            'qty' => $this->faker->numberBetween(1, 20),
            'amount' => $this->faker->numberBetween(100, 10000),
            'payment_method' => $this->faker->randomElement($payment_methods),
            'purchase_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ];
    }
}
