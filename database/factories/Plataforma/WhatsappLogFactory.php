<?php

namespace Database\Factories\Plataforma;

use App\Models\Plataforma\WhatsappLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class WhatsappLogFactory extends Factory
{
    protected $model = WhatsappLog::class;

    public function definition()
    {
        $responses = [
            'Mensaje enviado correctamente',
            'Error al enviar mensaje',
            'Cliente no encontrado',
            'Mensaje recibido',
            'Mensaje leído',
        ];

        return [
            'type' => $this->faker->randomElement(range(1, 5)),
            'clients' => $this->faker->randomElements(range(1, 100), $this->faker->numberBetween(1, 5)),
            'message' => $this->faker->paragraph(),
            'response' => $this->faker->randomElement($responses),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the log is of type message.
     */
    public function message()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'message',
                'message' => $this->faker->sentence(),
                'response' => 'Mensaje enviado correctamente'
            ];
        });
    }

    /**
     * Indicate that the log is of type campaign.
     */
    public function campaign()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'campaign',
                'message' => $this->faker->paragraph(),
                'response' => 'Campaña enviada a ' . count($attributes['clients']) . ' clientes'
            ];
        });
    }

    /**
     * Indicate that the log is of type error.
     */
    public function error()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'error',
                'message' => $this->faker->sentence(),
                'response' => 'Error: ' . $this->faker->sentence()
            ];
        });
    }
}
