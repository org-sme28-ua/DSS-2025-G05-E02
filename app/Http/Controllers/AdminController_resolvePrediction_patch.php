<?php
// ──────────────────────────────────────────────────────────────────────────────
// PATCH para AdminController::resolvePrediction()
//
// Localiza el bloque "if ($action === 'ganada')" y sustitúyelo por este.
// El único cambio es la llamada a Ranking::actualizarRankingUsuario al final
// del bloque 'ganada', y un apunte comentado en 'perdida'.
// ──────────────────────────────────────────────────────────────────────────────

                if ($action === 'ganada') {
                    $premio = (float) $bet->monto * (float) $bet->cuota;
                    $wallet->saldoDisponible = $balanceBefore + $premio;
                    $wallet->save();

                    $bet->estado = 'ganada';
                    $bet->resultado = $resultado ?: 'Predicción cumplida';
                    $bet->balance_despues = $wallet->saldoDisponible;
                    $message = 'Tu predicción ha sido marcada como ganada. Premio abonado: '
                        . number_format($premio, 2, ',', '.') . ' EUR.';

                    // ── Ranking: bonus predicción ganada ─────────────────────
                    // Multiplicador ×15 (mayor que juegos de azar ×10) para
                    // recompensar el mayor riesgo/dificultad de las predicciones.
                    // La ganancia neta es premio - monto (ya se descontó al crear).
                    $gananciaNeta = $premio - (float) $bet->monto;
                    $nuevosPuntos = (int) floor((float) $bet->monto * (float) $bet->cuota * 15);
                    \App\Models\Ranking::actualizarRankingUsuario($user, $gananciaNeta, $nuevosPuntos);
                    // ─────────────────────────────────────────────────────────

                } else {
                    // perdida: no se suman puntos extra; ya se dieron al enviar la predicción
                    $bet->estado = 'perdida';
                    $bet->resultado = $resultado ?: 'Predicción no cumplida';
                    $bet->balance_despues = $wallet->saldoDisponible;
                    $message = 'Tu predicción ha sido marcada como perdida.';
                }
